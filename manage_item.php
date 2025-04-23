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
$item_id = $_GET['edit'] ?? 0;

// DELETE
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // No ownership check; allow deletion of any item
    $conn->query("DELETE FROM tbl_item WHERE item_id = $delete_id");
    echo "<script>alert('Item deleted successfully'); window.location = 'manage_item.php';</script>";
    exit;
}

// FETCH FOR EDIT
$item = ['item_name' => '', 'item_img' => '', 'item_com' => ''];
if ($item_id > 0) {
    // No ownership check; allow editing of any item
    $res = $conn->query("SELECT * FROM tbl_item WHERE item_id = $item_id");
    if ($res && $res->num_rows > 0) {
        $item = $res->fetch_assoc();
    } else {
        echo "<script>alert('Item not found.'); window.location = 'manage_item.php';</script>";
        exit;
    }
}

// SAVE OR UPDATE
if (isset($_POST['btn_save'])) {
    $item_name = $_POST['txt_item_name'] ?? '';
    $item_com = $_POST['txt_item_comment'] ?? '';
    $item_img = $item['item_img'] ?? '';  // If no new image is uploaded, keep the existing one

    // Upload image if new one
    if (isset($_FILES['img_item']) && $_FILES['img_item']['error'] == 0) {
        $img_name = basename($_FILES['img_item']['name']);
        $upload_dir = "images/item_images/";
        $upload_path = $upload_dir . $img_name;
        if (move_uploaded_file($_FILES['img_item']['tmp_name'], $upload_path)) {
            $item_img = $img_name;  // Update the image if a new one is uploaded
        }
    }

    $item_name = $conn->real_escape_string($item_name);
    $item_com = $conn->real_escape_string($item_com);
    $item_img = $conn->real_escape_string($item_img);

    if ($item_id > 0) {
        // Update item (no user verification)
        $sql = "UPDATE tbl_item SET item_name='$item_name', item_img='$item_img', item_com='$item_com' WHERE item_id=$item_id";
    } else {
        // Insert new item
        $sql = "INSERT INTO tbl_item (item_name, item_img, item_com, user_id) VALUES ('$item_name', '$item_img', '$item_com', $user_id)";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Item saved successfully'); window.location = 'manage_item.php';</script>";
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
            <div class="panel-heading"><?= $item_id ? "Edit Item" : "Add New Item" ?></div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-body">                                         
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Item Name</label>
                                        <input type="text" name="txt_item_name" class="form-control" value="<?= $item['item_name']?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Comment</label>
                                        <textarea class="form-control" name="txt_item_comment" rows="3"><?= $item['item_com'] ?></textarea>
                                    </div>
                                </div>
                            </div>  
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Item Image</label>
                                        <input type="file" class="dropify" name="img_item" />
                                        <?php if ($item['item_img']) : ?>
                                            <img src="images/item_images/<?= $item['item_img'] ?>" alt="Item" width="80">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>                      
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="btn_save" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                            <a href="manage_item.php" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ITEM TABLE -->
<div class="row">
    <div class="col-sm-12">         
        <div class="white-box">
            <div class="panel panel-info">
                <div class="panel-heading"> Manage Item List</div>
            </div>                                                              

            <div class="table-responsive">
                <table id="myTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>SR NO.</th>
                            <th>ACTION</th>
                            <th>ITEM NAME</th>
                            <th>ITEM IMAGE</th>
                            <th>ITEM COMMENT</th>
                            <th>USER ID</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql = "SELECT * FROM tbl_item WHERE user_id = $user_id"; // No filter for user_id, so all items are shown
                        $result = $conn->query($sql);
                        $counter = 0;

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?= ++$counter ?></td>
                            <td class="text-nowrap">
                                <a href="manage_item.php?edit=<?= $row['item_id'] ?>" class='btn_edit' data-toggle="tooltip" data-original-title="Edit"> 
                                    <i class="fa fa-pencil text-inverse m-r-10"></i> 
                                </a>
                                <a href="manage_item.php?delete=<?= $row['item_id'] ?>" onclick="return confirm('Delete this item?')" class='btn_delete' data-toggle="tooltip" data-original-title="Delete"> 
                                    <i class="fa fa-close text-danger"></i> 
                                </a>
                            </td>
                            <td><?= $row['item_name'] ?></td>
                            <td>
                                <img src="images/item_images/<?= $row['item_img'] ?>" width="80">
                            </td>
                            <td><?= $row['item_com'] ?></td>
                            <td><?= $row['user_id'] ?></td>
                        </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No items found.</td></tr>";
                        }
                        $conn->close();
                    ?>                                                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>      
</div>
