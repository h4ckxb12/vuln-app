<?php
session_start();
include('includes/header.php');
include_once('config/db.php');

// Get the order ID from session or URL
$orderId = $_SESSION['order_id'] ?? null;

if ($orderId) {
    // Fetch order details from database
    $orderSql = "SELECT * FROM orders WHERE order_id = '$orderId'";
    $orderResult = $conn->query($orderSql);
    if ($orderResult->num_rows > 0) {
        $order = $orderResult->fetch_assoc();
        $orderItemsSql = "SELECT * FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = '$orderId'";
        $orderItemsResult = $conn->query($orderItemsSql);
        $orderItems = [];
        while ($item = $orderItemsResult->fetch_assoc()) {
            $orderItems[] = $item;
        }
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">
                <h3>Thank you for your purchase!</h3>
                <p>Your order has been placed successfully. You will receive an email with your order details shortly.</p>

                <?php if ($orderId && isset($order)): ?>
                    <h4>Order Summary</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalAmount = 0;
                            foreach ($orderItems as $item): 
                                $itemTotal = $item['quantity'] * $item['product_price'];
                                $totalAmount += $itemTotal;
                            ?>
                                <tr>
                                    <td><?= $item['product_name'] ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>$<?= number_format($itemTotal, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h3>Total: $<?= number_format($totalAmount, 2) ?></h3>
                <?php endif; ?>

                <a href="dashboard.php" class="btn btn-primary">Go to Home</a>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
