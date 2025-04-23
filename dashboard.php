<?php
session_start();
require_once "config.php";
include('includes/header.php');

// Handle feedback deletion (IDOR, no auth check, no CSRF)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM feedback WHERE id = '$delete_id'";
    mysqli_query($conn, $query);
    
    // Using JavaScript to redirect after deletion
    echo "<script>
            alert('Feedback deleted successfully!');
            window.location.href = 'dashboard.php';
          </script>";
    exit;
}

 // Include the header after the redirection

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$username = $row_login_select['username'];;

// Handle feedback submission (stored XSS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback'])) {
    $feedback_msg = $_POST['feedback'];
    $user = $username;
    $insert = "INSERT INTO feedback (username, message) VALUES ('$user', '$feedback_msg')";
    mysqli_query($conn, $insert);
}
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"> Welcome to BugQuell-Vuln-App </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <h3>Hello, <?php echo $username; ?>!</h3>
                    <p>This dashboard simulates a vulnerable environment for learning and practicing web application security. Explore and test various features below.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fake Analytics -->
<div class="row">
    <div class="col-md-4">
        <div class="white-box">
            <h3 class="box-title">Total Users</h3>
            <ul class="list-inline two-part">
                <li><i class="fa fa-users text-info"></i></li>
                <li class="text-right"><span class="counter">1337</span></li>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="white-box">
            <h3 class="box-title">Items Listed</h3>
            <ul class="list-inline two-part">
                <li><i class="fa fa-cube text-purple"></i></li>
                <li class="text-right"><span class="counter">420</span></li>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="white-box">
            <h3 class="box-title">Feedback</h3>
            <ul class="list-inline two-part">
                <li><i class="fa fa-comments text-success"></i></li>
                <li class="text-right"><span class="counter">69</span></li>
            </ul>
        </div>
    </div>
</div>

<!-- Quick Access Links -->
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Quick Access</h3>
            <div class="row">
                <div class="col-md-3"><a href="manage_user.php" class="btn btn-info btn-block">Manage Users</a></div>
                <div class="col-md-3"><a href="manage_item.php" class="btn btn-warning btn-block">View Items</a></div>
                <div class="col-md-3"><a href="profile.php" class="btn btn-success btn-block">My Profile</a></div>
                <div class="col-md-3"><a href="logout.php" class="btn btn-danger btn-block">Logout</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Form -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-warning">
            <div class="panel-heading"> Leave Feedback (Vulnerable to XSS!) </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="feedback">Your Message:</label>
                            <textarea name="feedback" class="form-control" rows="3" placeholder="Try inserting <script>alert(1)</script>"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Wall -->
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Feedback Wall</h3>
            <div class="list-group">
                <?php
                $result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY submitted_at DESC");
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $fid = $row['id'];
                        echo "<div class='list-group-item'>
                                <strong>{$row['username']}</strong>: {$row['message']}
                                <span class='pull-right text-muted'>
                                    <small>{$row['submitted_at']}</small> |
                                    <a href='?delete_id={$fid}' onclick='return confirm(\"Delete this feedback?\")' style='color:red;'>Delete</a>
                                </span>
                              </div>";
                    }
                } else {
                    echo "<div class='list-group-item'>No feedback yet. Be the first to leave one!</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
