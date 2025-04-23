<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | BugQuell-vuln-php-app</title>
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
        .login-box {
            max-width: 400px;
            margin: 80px auto;
        }
        .white-box {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <section id="wrapper" class="login-register">
        <div class="login-box">
            <div class="white-box">
                <form class="form-horizontal form-material" id="loginform" action="login_process.php" method="POST">
                    <h3 class="box-title m-b-20 text-center">Sign In to BugQuell-vuln-php-app</h3>

                    <?php if (isset($_SESSION['login_error'])): ?>
                        <p class="error text-center"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></p>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['2fa_pending'])): ?>
                        <!-- Normal login step -->
                        <div class="form-group">
                            <input class="form-control" type="text" name="username" placeholder="Username" required>
                        </div>

                        <div class="form-group">
                            <input class="form-control" type="password" name="password" placeholder="Password" required>
                        </div>

                        <div class="form-group text-center m-t-20">
                            <button class="btn btn-info btn-lg btn-block text-uppercase" type="submit" name="login">Log In</button>
                        </div>
                    <?php else: ?>
                        <!-- 2FA step -->
                        <div class="form-group">
                            <input class="form-control" type="text" name="code" placeholder="Enter 2FA Code" required>
                        </div>

                        <div class="form-group text-center m-t-20">
                            <button class="btn btn-info btn-lg btn-block text-uppercase" type="submit" name="verify_2fa">Verify Code</button>
                        </div>
                    <?php endif; ?>

                    <div class="form-group m-b-0 text-center">
                        <p>Don't have an account? <a href="register.php" class="text-primary"><b>Sign Up</b></a></p>
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
