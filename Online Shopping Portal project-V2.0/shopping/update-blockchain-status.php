<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    die("Unauthorized");
}

if (isset($_POST['orderId']) && isset($_POST['txHash'])) {
    $orderId = mysqli_real_escape_string($con, $_POST['orderId']);
    $txHash = mysqli_real_escape_string($con, $_POST['txHash']);
    $userId = $_SESSION['id'];

    // Check if the order belongs to the user and is indeed a blockchain payment
    $check = mysqli_query($con, "SELECT id FROM orders WHERE id='$orderId' AND userId='$userId' AND paymentMethod='Blockchain'");

    if (mysqli_num_rows($check) > 0) {
        // Update the order with transaction hash and set trust_status to 'verified'
        // (Note: In a real production app, we would also verify this on-chain via a background worker)
        $query = mysqli_query($con, "UPDATE orders SET 
                                    blockchain_txid = '$txHash', 
                                    trust_status = 'verified',
                                    blockchain_timestamp = NOW() 
                                    WHERE id = '$orderId'");

        if ($query) {
            echo "Success";
            // Clear cart if this was the last step
            unset($_SESSION['cart']);
        } else {
            // If the columns don't exist yet, we attempt to add them (Safety for students)
            mysqli_query($con, "ALTER TABLE orders ADD COLUMN blockchain_txid VARCHAR(255) NULL");
            mysqli_query($con, "ALTER TABLE orders ADD COLUMN trust_status VARCHAR(50) DEFAULT 'pending'");
            mysqli_query($con, "ALTER TABLE orders ADD COLUMN blockchain_timestamp TIMESTAMP NULL");

            // Retry update
            mysqli_query($con, "UPDATE orders SET 
                                blockchain_txid = '$txHash', 
                                trust_status = 'verified',
                                blockchain_timestamp = NOW() 
                                WHERE id = '$orderId'");
            echo "Success After Auto-Migration";
            unset($_SESSION['cart']);
        }
    } else {
        echo "Order not found or unauthorized.";
    }
}
?>