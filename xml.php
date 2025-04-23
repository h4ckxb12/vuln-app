<?php
session_start();
require_once "config.php"; // Include the database configuration

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle feedback form submission (XSS vulnerability)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback'])) {
    $feedback = $_POST['feedback'];
    $username = $_SESSION['uname'] ?? 'Anonymous';  // Assuming the user is logged in

    // Store the feedback (XSS vulnerability: no sanitization)
    $sql = "INSERT INTO feedback (username, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $feedback);
    $stmt->execute();
    $stmt->close();
}

// Handle XML upload (XML Injection vulnerability)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xml_file'])) {
    $file = $_FILES['xml_file'];

    // Basic file validation (only allow XML files)
    if ($file['type'] == 'text/xml') {
        // Enable external entity loading (vulnerable to XXE)
        libxml_disable_entity_loader(false);  // Allow loading external entities

        // Disable internal error handling (to let attackers see detailed errors)
        libxml_use_internal_errors(false);

        // Load the XML file (vulnerable)
        $xml = simplexml_load_file($file['tmp_name'], 'SimpleXMLElement', LIBXML_NOENT | LIBXML_DTDLOAD);

        // Check if there were parsing errors
        if ($xml === false) {
            echo "Failed to load XML file. Errors: <br>";
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo "Error: " . $error->message . "<br>";
            }
            libxml_clear_errors();
        } else {
            // Display the parsed XML content (unsafe)
            echo "<pre>";
            print_r($xml);
            echo "</pre>";
        }
    } else {
        echo "Only XML files are allowed.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable App - XML Injection and XSS</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="page-header">Welcome to BugQuell-Vuln-App</h1>

        <!-- Feedback Form (Vulnerable to XSS) -->
        <div class="panel panel-warning">
            <div class="panel-heading">Leave Feedback (Vulnerable to XSS)</div>
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

        <!-- Feedback Wall -->
        <div class="panel panel-info">
            <div class="panel-heading">Feedback Wall</div>
            <div class="panel-body">
                <div class="list-group">
                    <?php
                    // Display feedback from the database
                    $result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY submitted_at DESC");
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='list-group-item'>
                                    <strong>{$row['username']}</strong>: {$row['message']}
                                    <span class='pull-right text-muted'>
                                        <small>{$row['submitted_at']}</small>
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

        <!-- XML Upload Form (Vulnerable to XML Injection) -->
        <div class="panel panel-danger">
            <div class="panel-heading">Upload XML (Vulnerable to XML Injection)</div>
            <div class="panel-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="xml_file">Select XML File:</label>
                        <input type="file" name="xml_file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-danger">Upload XML</button>
                </form>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('includes/footer.php'); ?>
