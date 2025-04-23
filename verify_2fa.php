<?php
session_start();
require_once "config.php";

$entered_code = $_POST['2fa_code'] ?? '';
$username = $_SESSION['pending_user'] ?? '';

if (empty($username) || empty($entered_code)) {
    $_SESSION['login_error'] = "Invalid session or 2FA code.";
    header("Location: index.php");
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all codes from DB for the user
$sql = "SELECT code FROM twofa_codes WHERE username = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$valid_code_found = false;

// Check if the entered code matches any of the codes for the user
while ($row = $result->fetch_assoc()) {
    if ($entered_code == $row['code']) {
        $valid_code_found = true;
        break;
    }
}

$stmt->close();
$conn->close();

if ($valid_code_found) {
    // 2FA successful
    $_SESSION['uname'] = $username;
    $_SESSION['user_logged_in'] = true;

    // Optional: Set is_admin if needed
    // $_SESSION['is_admin'] = 0;

    unset($_SESSION['2fa_code']);
    unset($_SESSION['pending_user']);

    header("Location: dashboard.php");
    exit;
} else {
    $_SESSION['login_error'] = "Invalid 2FA code.";
    header("Location: twofa_code.php");
    exit;
}
