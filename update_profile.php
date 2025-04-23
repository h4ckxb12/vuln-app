<?php
session_start(); // Start the session at the top

require_once "config.php"; // DB connection info
include('includes/header.php');

// DB connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$current_user = $_SESSION['uname'] ?? '';
if (!$current_user) {
    die("User not logged in.");
}

// Fetch current user info
$sql = "SELECT * FROM users WHERE username = '$current_user'";
$result = $conn->query($sql);
$row_login_select = $result->fetch_assoc();

// Handle form submission
if (isset($_POST['btn_update_profile'])) {
    $fname = $_POST['txt_fname'];
    $lname = $_POST['txt_lname'];
    $email = $_POST['txt_email'];
    $phone = $_POST['txt_mobile'];
    $twofa_enabled = isset($_POST['twofa_enabled']) ? 1 : 0;
    $img_name = $row_login_select['img']; // default to old image

    // Image upload
    if (!empty($_FILES["img"]["name"])) {
        $upload_dir = "images/user_images/";
        $old_image = $row_login_select['img'];

        // Remove old image if it exists
        if (!empty($old_image) && file_exists($upload_dir . $old_image)) {
            unlink($upload_dir . $old_image);
        }

        // Save new image
        $uploaded_img = $_FILES["img"]["name"];
        $img_name = pathinfo($uploaded_img, PATHINFO_FILENAME) . mt_rand(1000, 9999) . "." . pathinfo($uploaded_img, PATHINFO_EXTENSION);
        move_uploaded_file($_FILES["img"]["tmp_name"], $upload_dir . $img_name);
    }

    // Update profile without referencing twofa_secret
    $update_sql = "UPDATE users SET 
                    f_name = '$fname',
                    l_name = '$lname',
                    email = '$email',
                    phone_no = '$phone',
                    img = '$img_name',
                    twofa_enabled = '$twofa_enabled' 
                  WHERE username = '$current_user'";

    // Execute the update
    if ($conn->query($update_sql)) {
        echo "<script>alert('Profile updated successfully'); window.location = 'update_profile.php';</script>";
        exit;
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<div class="row bg-title">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
        <h4 class="page-title">My Profile</h4>
    </div>
</div>

<form class="form-horizontal form-material" name="frmMyProfile" id="frmMyProfile" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="white-box">
                <h3 class="box-title">User Profile Upload</h3>
                <input type="file" id="input-file-now-custom-1" class="dropify"
                       data-default-file="images/user_images/<?php echo !empty($row_login_select['img']) ? $row_login_select['img'] : ''; ?>"
                       name="img" />

                <?php if (!empty($row_login_select['img']) && file_exists('images/user_images/' . $row_login_select['img'])): ?>
                    <div style="margin-top: 20px;">
                        <label>Current Image Preview:</label><br>
                        <img src="images/user_images/<?php echo $row_login_select['img']; ?>"
                             alt="Profile Image"
                             style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px; border-radius: 5px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8 col-xs-12">
            <div class="white-box">
                <ul class="nav customtab nav-tabs" role="tablist">
                    <li role="presentation" class="nav-item">
                        <a href="#settings" class="nav-link active" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-cog"></i></span>
                            <span class="hidden-xs">My Profile</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="profile">
                        <div class="form-group">
                            <label class="col-md-12">Username</label>
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-line" name="txt_uname" id="txt_uname"
                                       value="<?php echo $row_login_select['username']; ?>" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">First Name</label>
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-line" name="txt_fname" id="txt_fname"
                                       value="<?php echo $row_login_select['f_name'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Last Name</label>
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-line" name="txt_lname" id="txt_lname"
                                       value="<?php echo $row_login_select['l_name'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Email</label>
                            <div class="col-md-12">
                                <input type="email" class="form-control form-control-line" name="txt_email" id="txt_email"
                                       value="<?php echo $row_login_select['email']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Mobile No</label>
                            <div class="col-md-12">
                                <input type="number" class="form-control form-control-line" id="txt_mobile" name="txt_mobile"
                                       value="<?php echo $row_login_select['phone_no']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Two-Factor Authentication (2FA)</label>
                            <div class="col-md-12">
                                <label class="switch">
                                    <input type="checkbox" name="twofa_enabled" <?php echo $row_login_select['twofa_enabled'] ? 'checked' : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success" id="btn_update_profile" name="btn_update_profile">Update Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Toggle Switch Style -->
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  border-radius: 50%;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
}
input:checked + .slider {
  background-color: #4CAF50;
}
input:checked + .slider:before {
  transform: translateX(24px);
}
</style>
<?php include('includes/footer.php'); ?>