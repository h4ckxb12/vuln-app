<?php
session_start(); // Start the session at the top

require_once "config.php"; // DB connection info

// Get user input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Prevent SQL injection by using prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify password (no hashing)
    if ($password == $user['password']) {

        // Check if 2FA is enabled
        if (!empty($user['twofa_enabled']) && $user['twofa_enabled'] == 1) {

            // Generate 2FA code
            $code = rand(100000, 999999);
            $_SESSION['2fa_code'] = $code;
            $_SESSION['pending_user'] = $user['username'];

            // Store 2FA code in the database
            $stmt = $conn->prepare("INSERT INTO twofa_codes (username, code) VALUES (?, ?)");
            $stmt->bind_param("si", $username, $code);
            $stmt->execute();
            $stmt->close();

            // Regenerate session ID to avoid session fixation
            session_regenerate_id();

            // Redirect to page where user can see 2FA code
            header("Location: twofa_code.php");
            exit;
        } else {
            // No 2FA, login directly
            $_SESSION['uname'] = $user['username'];
            $_SESSION['user_logged_in'] = true;
            $_SESSION['is_admin'] = $user['is_admin'];

            // Regenerate session ID to avoid session fixation
            session_regenerate_id();

            // Redirect to the dashboard or home page
            header("Location: dashboard.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: index.php");
    exit;
}
