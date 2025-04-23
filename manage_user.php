<?php
session_start();
include('includes/header.php');
require_once "config.php";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current user_id from the session (ensure this is set after login)
$user_id = $row_login_select['id'];  // This assumes the session contains the user ID
$user_id_to_edit = $_GET['edit'] ?? 0;

// DELETE
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // No ownership check; allow deletion of any user
    $conn->query("DELETE FROM users WHERE id = $delete_id");
    echo "<script>alert('User deleted successfully'); window.location = 'manage_user.php';</script>";
    exit;
}

// FETCH FOR EDIT
$user = ['username' => '', 'email' => '', 'f_name' => '', 'l_name' => '', 'phone_no' => '', 'address' => '', 'img' => ''];
if ($user_id_to_edit > 0) {
    // No ownership check; allow editing of any user
    $res = $conn->query("SELECT * FROM users WHERE id = $user_id_to_edit");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
    } else {
        echo "<script>alert('User not found.'); window.location = 'manage_user.php';</script>";
        exit;
    }
}

// SAVE OR UPDATE
if (isset($_POST['btn_save'])) {
    $username = $_POST['txt_username'] ?? '';
    $email = $_POST['txt_email'] ?? '';
    $f_name = $_POST['txt_f_name'] ?? '';
    $l_name = $_POST['txt_l_name'] ?? '';
    $phone_no = $_POST['txt_phone_no'] ?? '';
    $address = $_POST['txt_address'] ?? '';
    $img = $user['img'] ?? '';  // If no new image is uploaded, keep the existing one

    // Upload image if new one
    if (isset($_FILES['img_user']) && $_FILES['img_user']['error'] == 0) {
        $img_name = basename($_FILES['img_user']['name']);
        $upload_dir = "images/user_images/";
        $upload_path = $upload_dir . $img_name;
        if (move_uploaded_file($_FILES['img_user']['tmp_name'], $upload_path)) {
            $img = $img_name;  // Update the image if a new one is uploaded
        }
    }

    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $f_name = $conn->real_escape_string($f_name);
    $l_name = $conn->real_escape_string($l_name);
    $phone_no = $conn->real_escape_string($phone_no);
    $address = $conn->real_escape_string($address);
    $img = $conn->real_escape_string($img);

    if ($user_id_to_edit > 0) {
        // Update user
        $sql = "UPDATE users SET username='$username', email='$email', f_name='$f_name', l_name='$l_name', phone_no='$phone_no', address='$address', img='$img' WHERE id=$user_id_to_edit";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, f_name, l_name, phone_no, address, img) VALUES ('$username', '$email', '$f_name', '$l_name', '$phone_no', '$address', '$img')";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('User saved successfully'); window.location = 'manage_user.php';</script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- ADD/EDIT FORM -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?= $user_id_to_edit ? "Edit User" : "Add New User" ?></div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-body">                                         

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Username</label>
                                        <input type="text" name="txt_username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Email</label>
                                        <input type="email" name="txt_email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">First Name</label>
                                        <input type="text" name="txt_f_name" class="form-control" value="<?= htmlspecialchars($user['f_name']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Last Name</label>
                                        <input type="text" name="txt_l_name" class="form-control" value="<?= htmlspecialchars($user['l_name']) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Phone Number</label>
                                        <input type="text" name="txt_phone_no" class="form-control" value="<?= htmlspecialchars($user['phone_no']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Address</label>
                                        <input type="text" name="txt_address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Profile Image</label>
                                        <input type="file" class="dropify" name="img_user" />
                                        <?php if ($user['img']) : ?>
                                            <img src="images/user_images/<?= $user['img'] ?>" alt="User" width="80">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>                      
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="btn_save" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                            <a href="manage_user.php" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- USER TABLE -->
<div class="row">
    <div class="col-sm-12">         
        <div class="white-box">
            <div class="panel panel-info">
                <div class="panel-heading"> Manage User List</div>
            </div>                                                              

            <div class="table-responsive">
                <table id="myTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>SR NO.</th>
                            <th>ACTION</th>
                            <th>USERNAME</th>
                            <th>EMAIL</th>
                            <th>FIRST NAME</th>
                            <th>LAST NAME</th>
                            <th>PHONE NO.</th>
                            <th>ADDRESS</th>
                            <th>PROFILE IMAGE</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql = "SELECT * FROM users";  // Display all users
                        $result = $conn->query($sql);
                        $counter = 0;

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?= ++$counter ?></td>
                            <td class="text-nowrap">
                                <a href="manage_user.php?edit=<?= $row['id'] ?>" class='btn_edit' data-toggle="tooltip" data-original-title="Edit"> 
                                    <i class="fa fa-pencil text-inverse m-r-10"></i> 
                                </a>
                                <a href="manage_user.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')" class='btn_delete' data-toggle="tooltip" data-original-title="Delete"> 
                                    <i class="fa fa-close text-danger"></i> 
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['f_name']) ?></td>
                            <td><?= htmlspecialchars($row['l_name']) ?></td>
                            <td><?= htmlspecialchars($row['phone_no']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td>
                                <img src="images/user_images/<?= htmlspecialchars($row['img']) ?>" width="80">
                            </td>
                        </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='9'>No users found.</td></tr>";
                        }
                        $conn->close();
                    ?>                                                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>      
</div>
