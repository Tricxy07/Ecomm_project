<?php
session_start();
require('./includes/config.php');

$today_date = date("j,n,Y");

// Function to generate a serial number (UUID style)
function generateSerialNumber($productId) {
    $uuid = mt_rand(1, 200);
    $timestamp = time();
    return "{$uuid}-{$productId}-{$timestamp}";
}

// Check if it's a single product or cart order
if (!isset($_GET['q'])) {
    // 🔹 SINGLE PRODUCT PAYMENT

    $uuid = generateSerialNumber($_GET['id']);
    $productId = $_GET['id'];

    $sqlProduct = "SELECT * FROM products WHERE product_id='{$productId}'";
    $resultProduct = $conn->query($sqlProduct);
    $product = $resultProduct->fetch_assoc();

    $price = $product['product_price'] + 50; // Add shipping
    $discVal = $product['product_price'] > 50000 ? '(1)free repairing' : '50% discount';

    $sqlSold = "INSERT INTO soldProducts (uid, pid, price, date) VALUES (
        {$_SESSION['id']},
        {$productId},
        {$price},
        '{$today_date}'
    )";

    $sqlService = "INSERT INTO servicestatus (uid, discount, pid, uuid) VALUES (
        {$_SESSION['id']},
        '{$discVal}',
        {$productId},
        '{$uuid}'
    )";

    $conn->query($sqlSold);
    $conn->query($sqlService);

} else {
    // 🔸 CART PAYMENT (UPI or COD)

    $decodedProdId = unserialize(urldecode($_GET['id']));
    $decodedQuantity = unserialize(urldecode($_GET['q']));

    for ($i = 0; $i < count($decodedProdId); $i++) {
        $uuid = generateSerialNumber($decodedProdId[$i]);

        $sqlProduct = "SELECT * FROM products WHERE product_id='{$decodedProdId[$i]}'";
        $resultProduct = $conn->query($sqlProduct);
        $product = $resultProduct->fetch_assoc();

        $discVal = $product['product_price'] > 50000 ? '(1)free repairing' : '50% discount';

        $sqlSold = "INSERT INTO soldProducts (uid, pid, price, quantity, date) VALUES (
            {$_SESSION['id']},
            {$decodedProdId[$i]},
            {$product['product_price']},
            {$decodedQuantity[$i]},
            '{$today_date}'
        )";

        $sqlService = "INSERT INTO servicestatus (uid, discount, pid, uuid) VALUES (
            {$_SESSION['id']},
            '{$discVal}',
            {$decodedProdId[$i]},
            '{$uuid}'
        )";

        $conn->query($sqlSold);
        $conn->query($sqlService);
    }

    // Delete cart after successful order
    $conn->query("DELETE FROM carts WHERE uid={$_SESSION['id']}");
}

$conn->close();

// Redirect to confirmation
header("Location: message.php");
exit();
?>
