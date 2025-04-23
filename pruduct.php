<?php
session_start();

// Example products
$products = [
    [
        'id' => 1,
        'name' => 'Product 1',
        'price' => 50,
        'image' => 'https://via.placeholder.com/200x200.png?text=Product+1'
    ],
    [
        'id' => 2,
        'name' => 'Product 2',
        'price' => 70,
        'image' => 'https://via.placeholder.com/200x200.png?text=Product+2'
    ],
    [
        'id' => 3,
        'name' => 'Product 3',
        'price' => 90,
        'image' => 'https://via.placeholder.com/200x200.png?text=Product+3'
    ]
];

// Handle adding products to the cart
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];
    
    // Check if the product is already in the cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add the product to the cart
    $_SESSION['cart'][] = $product_id;
    header('Location: product.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="mt-4">Our Products</h1>
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4">
            <div class="card">
                <img src="<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= $product['name'] ?></h5>
                    <p class="card-text">Price: $<?= $product['price'] ?></p>
                    <a href="product.php?add_to_cart=<?= $product['id'] ?>" class="btn btn-primary">Add to Cart</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <a href="cart.php" class="btn btn-success mt-3">Go to Cart</a>
</div>

</body>
</html>
