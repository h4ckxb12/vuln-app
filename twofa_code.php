<?php
session_start();

require_once "config.php";
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if pending_user exists
if (!isset($_SESSION['pending_user'])) {
    die("Invalid session. Please log in again.");
}

$username = $_SESSION['pending_user'];
$code = 'N/A'; // Default value in case fetch fails

$sql = "SELECT code FROM twofa_codes WHERE username = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($code);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2FA Code - BugQuell-vuln-php-app</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Your 2FA Code</h2>
        <div class="alert alert-info">
            <p><strong>Code:</strong> <?php echo $code; ?></p>
        </div>
        <form action="verify_2fa.php" method="POST">
            <div class="form-group">
                <label for="2fa_code">Enter 2FA Code</label>
                <input type="text" class="form-control" id="2fa_code" name="2fa_code" required>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </div>
</body>
</html>
