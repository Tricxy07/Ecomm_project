<?php
session_start();
include_once('./includes/headerNav.php');
include_once('./includes/config.php');

if (!isset($_SESSION['id'])) {
    header("location:index.php?error=unauthorized");
    exit();
}

$total = 0;
$pidArray = [];
$quantArray = [];

$sql = "SELECT * FROM carts WHERE uid={$_SESSION['id']} AND status='active'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pidArray[] = $row['pid'];
        $quantArray[] = $row['quantity'];
        $total += $row["price"] * $row["quantity"];
    }
}

$encodedPid = urlencode(serialize($pidArray));
$encodedQuant = urlencode(serialize($quantArray));
$finalTotal = $total + 50; // Add shipping cost
?>

<h2 style="text-align:center;">Cash on Delivery (COD) Payment</h2>

<div style="display:flex;justify-content:center;flex-direction:column;align-items:center;">
    <p>Total Amount: ₹<?php echo $finalTotal; ?> (including ₹50 shipping)</p>
    
    <p>Choose Cash on Delivery to complete your order.</p>

    <!-- COD Confirmation Form -->
    <form action="paymentVerify.php?id=<?php echo $encodedPid; ?>&q=<?php echo $encodedQuant; ?>&method=cod" method="post">
        <button type="submit" style="padding:10px 20px;background-color:#007bff;color:white;border:none;border-radius:4px;">
            💵 Confirm COD
        </button>
    </form>
    
    <p>Note: Your order will be shipped and you will need to pay in cash when the product is delivered.</p>
</div>

<?php include_once('./includes/footer.php'); ?>
