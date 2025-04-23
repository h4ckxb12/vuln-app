<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $target = "uploads/" . basename($_FILES['file']['name']); // No file type checks (vulnerable)
    move_uploaded_file($_FILES['file']['tmp_name'], $target);
    echo "File uploaded to $target";
}
include('includes/header.php');
?>
<div class="container">
    <h2>File Upload</h2>
    <form method="POST" enctype="multipart/form-data">
        Select file: <input type="file" name="file" /><br/><br/>
        <input type="submit" value="Upload" />
    </form>
</div>
<?php include('includes/footer.php'); ?>