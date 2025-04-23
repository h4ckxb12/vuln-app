<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['uname'])) {
    header('Location: login.php');
    exit;
}

// Generate a random 2FA code
$twofa_code = rand(100000, 999999);  // 6-digit random code

// Store the code in session for later verification
$_SESSION['2fa_code'] = $twofa_code;
$_SESSION['2fa_expiration'] = time() + 300;  // 5 minutes expiration

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Code</title>
    <style>
        table {
            width: 50%;
            margin: 50px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Your 2FA Code</h2>

<table>
    <tr>
        <th>User</th>
        <td><?php echo $_SESSION['uname']; ?></td>
    </tr>
    <tr>
        <th>2FA Code</th>
        <td><?php echo $twofa_code; ?></td>
    </tr>
    <tr>
        <th>Expiration Time</th>
        <td><?php echo date('Y-m-d H:i:s', $_SESSION['2fa_expiration']); ?></td>
    </tr>
</table>

<p style="text-align: center;">This code will expire in 5 minutes.</p>

</body>
</html>
