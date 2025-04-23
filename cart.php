<?php
session_start();
include_once('config/db.php');
include 'includes/header.php';

// Sample cart structure: $_SESSION['cart'] = [product_id => quantity]
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$products = [];

if (!empty($cartItems)) {
    $ids = implode(',', array_keys($cartItems));  // Get the product IDs from the cart
    $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $cartItems[$row['product_id']];  // Store quantity for each product
        $products[] = $row;
    }
}
?>

<!-- Display cart items as before -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-9 col-lg-9 col-sm-7">
            <div class="panel panel-info">
                <div class="panel-heading">Your Cart (<?= count($cartItems) ?> item<?= count($cartItems) !== 1 ? 's' : '' ?>)</div>
                <div class="panel-wrapper collapse in show">
                    <div class="panel-body">
                        <?php if (count($products) > 0): ?>
                            <div class="table-responsive">
                                <table class="table product-overview">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Product info</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th style="text-align:center">Total</th>
                                            <th style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grandTotal = 0;
                                        foreach ($products as $product):
                                            $total = $product['product_price'] * $product['quantity'];
                                            $grandTotal += $total;
                                        ?>
                                        <tr>
                                            <td width="150"><img src="images/products/<?= $product['product_image'] ?>" alt="<?= $product['product_name'] ?>" width="80"></td>
                                            <td width="550">
                                                <h5 class="font-500"><?= $product['product_name'] ?></h5>
                                                <p><?= $product['product_description'] ?></p>
                                            </td>
                                            <td>$<?= number_format($product['product_price'], 2) ?></td>
                                            <td width="70">
                                                <input type="text" class="form-control" value="<?= $product['quantity'] ?>" readonly>
                                            </td>
                                            <td width="150" align="center" class="font-500">$<?= number_format($total, 2) ?></td>
                                            <td align="center">
                                                <a href="remove_from_cart.php?id=<?= $product['product_id'] ?>" class="text-inverse" title="Delete">
                                                    <i class="ti-trash text-dark"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <hr>
                                <a href="checkout.php" class="btn btn-danger pull-right"><i class="fa fa-shopping-cart"></i> Checkout</a>
                                <a href="product.php" class="btn btn-default btn-outline"><i class="fa fa-arrow-left"></i> Continue shopping</a>
                            </div>
                        <?php else: ?>
                            <p>Your cart is empty.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-lg-3 col-sm-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">Cart Summary</h3>
                        <hr>
                        <small>Total Price</small>
                        <h2>$<?= number_format($grandTotal ?? 0, 2) ?></h2>
                        <hr>
                        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                        <a href="clear_cart.php" class="btn btn-default btn-outline">Cancel</a>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">For Any Support</h3>
                        <hr>
                        <h4><i class="ti-mobile"></i> 9998979695 (Toll Free)</h4>
                        <small>Please contact with us if you have any questions. We are available 24h.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
