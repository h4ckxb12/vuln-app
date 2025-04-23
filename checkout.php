<?php
session_start();
include_once('config/db.php');
include 'includes/header.php';

// Sample cart structure: $_SESSION['cart'] = [product_id => quantity]
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$products = [];
$promoCode = "DISCOUNT10";  // Example promo code
$discountPercentage = 10;   // Example discount percentage
$discountApplied = false;
$discountAmount = 0;
$grandTotalAfterDiscount = 0;

// Track the total discount applied so far
if (!isset($_SESSION['total_discount'])) {
    $_SESSION['total_discount'] = 0;
}

if (!empty($cartItems)) {
    $ids = implode(',', array_keys($cartItems));  // Get the product IDs from the cart
    $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $cartItems[$row['product_id']];  // Store quantity for each product
        $products[] = $row;
    }
}

if (isset($_POST['applyPromo'])) {
    $inputPromoCode = $_POST['promo_code'];
    if ($inputPromoCode === $promoCode) {
        $discountApplied = true;
        // Calculate the discount based on current total amount
        $totalAmount = array_sum(array_map(function($product) {
            return $product['product_price'] * $product['quantity'];
        }, $products));
        
        // Calculate discount for the current total
        $discountAmount = ($totalAmount * $discountPercentage) / 100;

        // Accumulate the discount
        $_SESSION['total_discount'] += $discountAmount;
        
        // Calculate the new grand total after discount
        $grandTotalAfterDiscount = $totalAmount - $_SESSION['total_discount'];
    } else {
        echo "<script>alert('Invalid promo code!');</script>";
    }
}

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-9 col-lg-9 col-sm-7">
            <div class="panel panel-info">
                <div class="panel-heading">Checkout</div>
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
                                            <td width="70"><?= $product['quantity'] ?></td>
                                            <td width="150" align="center" class="font-500">$<?= number_format($total, 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <hr>
                                <h3>Total Amount: $<?= number_format($grandTotal, 2) ?></h3>

                                <?php if ($_SESSION['total_discount'] > 0): ?>
                                    <hr>
                                    <div class="alert alert-success">
                                        <h4>Promo Code Applied!</h4>
                                        <p>Promo Code: <strong><?= $promoCode ?></strong></p>
                                        <p>Total Discount: <strong>$<?= number_format($_SESSION['total_discount'], 2) ?></strong></p>
                                        <h3><strong>Grand Total After Discount: $<?= number_format($grandTotal - $_SESSION['total_discount'], 2) ?></strong></h3>
                                    </div>
                                <?php endif; ?>

                                <hr>
                                <!-- Promo Code Section -->
                                <form method="POST" action="checkout.php">
                                    <div class="form-group">
                                        <label for="promo_code">Promo Code</label>
                                        <input type="text" name="promo_code" id="promo_code" class="form-control" placeholder="Enter promo code">
                                    </div>
                                    <button type="submit" name="applyPromo" class="btn btn-primary">Apply Promo Code</button>
                                </form>

                                <!-- Payment Options Section -->
                                <h3>Choose Payment Option</h3>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active nav-item"><a href="#ipaypal" class="nav-link" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Paypal</span></a></li>
                                    <li role="presentation" class="nav-item"><a href="#icash" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-wallet"></i></span> <span class="hidden-xs">Cash on Delivery</span></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane" id="ipaypal">
                                        You can pay your money through PayPal. For more info, <a href="#">click here</a>.
                                        <br><br>
                                        <button class="btn btn-info"><i class="fa fa-cc-paypal"></i> Pay with PayPal</button>
                                    </div>
                                    <div role="tabpanel" class="tab-pane active" id="icash">
                                        <div class="col-md-7 col-sm-5">
                                            <form method="POST" action="checkout.php">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">CARD NUMBER</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="fa fa-credit-card"></i></div>
                                                        <input type="text" class="form-control" id="exampleInputuname" placeholder="Card Number"> </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-7 col-md-7">
                                                        <div class="form-group">
                                                            <label>EXPIRATION DATE</label>
                                                            <input type="text" class="form-control" name="Expiry" placeholder="MM / YY" required=""> </div>
                                                    </div>
                                                    <div class="col-xs-5 col-md-5 pull-right">
                                                        <div class="form-group">
                                                            <label>CV CODE</label>
                                                            <input type="text" class="form-control" name="CVC" placeholder="CVC" required=""> </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <label>NAME OF CARD</label>
                                                            <input type="text" class="form-control" name="nameCard" placeholder="NAME AND SURNAME"> </div>
                                                    </div>
                                                </div>
                                                <button class="btn btn-info" type="submit" name="placeOrder">Make Payment</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
                        <?php if ($_SESSION['total_discount'] > 0): ?>
                            <h4>Discount Applied!</h4>
                            <h3><strong>Grand Total: $<?= number_format($grandTotal - $_SESSION['total_discount'], 2) ?></strong></h3>
                        <?php endif; ?>
                        <hr>
                        <!-- Button to Place Order -->
                        <button class="btn btn-success" onclick="confirmOrder()">Place Order</button>
                        <a href="clear_cart.php" class="btn btn-default btn-outline">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Place order functionality (clear cart)
if (isset($_POST['placeOrder'])) {
    // Your order processing code here (e.g., saving to database, etc.)
    
    // Simulate order being placed
    echo "<script>alert('Your order will be placed.');</script>";

    // Clear the cart after order is placed
    unset($_SESSION['cart']);
    unset($_SESSION['total_discount']);  // Reset the discount after order

    // Redirect to confirmation or thank you page
    echo "<script>window.location.href = 'thank_you.php';</script>";
}

include 'includes/footer.php';
?>
