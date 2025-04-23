<?php
session_start();
require 'config/db.php'; // Make sure this sets up $conn

if (isset($_POST['btn_save'])) {
    // Simple form data validation
    $username = $_POST['username'];
    $password = $_POST['pass'];
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $id = $_POST['id'] ?? '';

    if ($id == '') {
        // Basic duplicate check (optional, you can improve this)
        $checkQuery = "SELECT * FROM users WHERE username = '$username'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $_SESSION['register_error'] = "Username already exists.";
        } else {
            // Insert query
            $sql_insert = "INSERT INTO users (`username`, `password`, `email`, `f_name`, `l_name`, `phone_no`, `address`)
                           VALUES ('$username', '$password', '$email', '$fname', '$lname', '$phone', '$address')";
            if (mysqli_query($conn, $sql_insert)) {
                $_SESSION['register_success'] = "Registration successful!";
                header("Location: register.php");
                exit();
            } else {
                $_SESSION['register_error'] = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | vuln-php-app</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Elite Admin CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/colors/default.css" id="theme" rel="stylesheet">

    <style>
        body {
            background: #f1f1f1;
        }
        .register-box {
            max-width: 500px;
            margin: 60px auto;
        }
        .white-box {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .error, .success {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>

<body>
    <section id="wrapper" class="login-register">
        <div class="register-box">
            <div class="white-box">
                <form class="form-horizontal form-material" method="POST" action="register.php">
                    <h3 class="box-title m-b-20 text-center">Register for vuln-php-app</h3>

                    <?php if (isset($_SESSION['register_error'])): ?>
                        <p class="error"><?php echo $_SESSION['register_error']; unset($_SESSION['register_error']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['register_success'])): ?>
                        <p class="success"><?php echo $_SESSION['register_success']; unset($_SESSION['register_success']); ?></p>
                    <?php endif; ?>

                    <div class="form-group">
                        <input class="form-control" type="text" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="pass" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="fname" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="lname" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="address" placeholder="Address" rows="3" required></textarea>
                    </div>

                    <div class="form-group text-center m-t-20">
                        <button class="btn btn-success btn-lg btn-block text-uppercase" name="btn_save" type="submit">Register</button>
                    </div>

                    <div class="form-group m-b-0 text-center">
                        <p>Already have an account? <a href="index.php" class="text-primary"><b>Log In</b></a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
