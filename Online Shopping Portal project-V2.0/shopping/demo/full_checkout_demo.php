<?php
// Full Checkout Demo
// Simulates a logged-in user, creates an order and order details.
session_start();
require_once __DIR__ . '/../includes/config.php';

// Adjust these values as needed for the demo
$demoUserId = 1;
$demoAddressId = 1;
$products = [
    // productId => quantity
    1 => 1,
    2 => 2
];

// Ensure DB connection ($con from includes/config.php)
if (!$con) {
    echo "Database connection not available.\n";
    exit; 
}

// Build order
$orderno = mt_rand(100000000,999999999);
$totalamount = 0.0;
foreach ($products as $pid => $qty) {
    $r = mysqli_query($con, "SELECT productPrice FROM products WHERE id='".intval($pid)."'");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $price = floatval($row['productPrice']);
        $totalamount += $price * intval($qty);
    } else {
        echo "Product ID {$pid} not found.\n";
        exit;
    }
}

$txnType = 'Demo';
$txnNo = 'DEMO'.time();

// Determine schema: some installs store one order row per product (no orderNumber), others have order header/details
$colsRes = mysqli_query($con, "SHOW COLUMNS FROM orders") ;
$cols = [];
while ($c = mysqli_fetch_assoc($colsRes)) { $cols[] = $c['Field']; }

$detailErrors = [];
if (in_array('orderNumber', $cols)) {
    // Modern schema: insert header + details
    $insertOrder = "INSERT INTO orders (orderNumber, userId, addressId, totalAmount, txnType, txnNumber, orderStatus) VALUES ('".mysqli_real_escape_string($con, $orderno)."', '".intval($demoUserId)."', '".intval($demoAddressId)."', '".mysqli_real_escape_string($con, $totalamount)."', '".mysqli_real_escape_string($con, $txnType)."', '".mysqli_real_escape_string($con, $txnNo)."', 'Processing')";
    if (!mysqli_query($con, $insertOrder)) {
        echo "Failed to create order: " . mysqli_error($con) . "\n";
        exit;
    }
    $orderId = mysqli_insert_id($con);

    // Insert order details
    foreach ($products as $pid => $qty) {
        $sql = "INSERT INTO ordersdetails (orderId, userId, productId, quantity, price) VALUES ('".intval($orderId)."', '".intval($demoUserId)."', '".intval($pid)."', '".intval($qty)."', (SELECT productPrice FROM products WHERE id='".intval($pid)."'))";
        if (!mysqli_query($con, $sql)) {
            $detailErrors[] = mysqli_error($con);
        }
    }

    // Clear any cart rows for demo user (if cart table exists)
    @mysqli_query($con, "DELETE FROM cart WHERE userID='".intval($demoUserId)."'");

} else {
    // Legacy schema: insert one row per product into orders
    $orderId = null;
    foreach ($products as $pid => $qty) {
        $sql = "INSERT INTO orders (userId, productId, quantity, paymentMethod, orderStatus) VALUES ('".intval($demoUserId)."', '".intval($pid)."', '".intval($qty)."', '".mysqli_real_escape_string($con, $txnType)."', 'Processing')";
        if (!mysqli_query($con, $sql)) {
            $detailErrors[] = mysqli_error($con);
        } else {
            $orderId = mysqli_insert_id($con); // last inserted id
        }
    }
    @mysqli_query($con, "DELETE FROM cart WHERE userID='".intval($demoUserId)."'");
}

// Output result
echo "<h2>Full Checkout Demo Completed</h2>\n";
echo "<p>Created Order ID: <strong>{$orderId}</strong></p>\n";
echo "<p>Order Number: <strong>{$orderno}</strong></p>\n";
echo "<p>Total Amount: <strong>{$totalamount}</strong></p>\n";
if (!empty($detailErrors)) {
    echo "<p><strong>Errors inserting details:</strong></p><pre>" . htmlspecialchars(implode("\n", $detailErrors)) . "</pre>\n";
} else {
    echo "<p>Order details inserted successfully.</p>\n";
}

echo "<p><a href='../my-orders.php'>Go to My Orders</a></p>\n";

mysqli_close($con);

?>

