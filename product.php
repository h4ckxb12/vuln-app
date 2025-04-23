<?php
session_start();
include('includes/header.php'); // Your header include
require_once "config.php";

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['product_id'],
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'image' => 'images/products/' . $row['product_image'] // Adjust if needed
        ];
    }
}
?>

<!-- Product Display Section -->
<div class="container">
    <h1 class="mt-4 mb-4 text-center">Our Products</h1>
    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text">Price: $<?= number_format($product['price'], 2) ?></p>
                            <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn btn-primary">Add to Cart</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No products available.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="text-center">
        <a href="cart.php" class="btn btn-success mt-3">Go to Cart</a>
    </div>
</div>

<?php include('includes/footer.php'); ?>
