<?php
session_start();  // Start the session to check login status

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If not an admin or not logged in, redirect to the login page
    header("Location: index.php");
    exit;
}

include('includes/header.php'); // Include header

?>

<h2>Admin Panel</h2>
<p>Welcome to the admin panel. Here you can manage users and settings.</p>

<?php
// Admin-specific content goes here

include('includes/footer.php'); // Include footer
?>
