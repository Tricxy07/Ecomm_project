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
$finalTotal = $total + 50;
?>

<h2 style="text-align:center;">Scan and Pay</h2>
<div style="display:flex;justify-content:center;flex-direction:column;align-items:center;">
    <p>Total Amount: ₹<?php echo $finalTotal; ?> (including ₹50 shipping)</p>
    
    <!-- 👇 Replace with your actual QR code image -->
    <img src="QR.jpg" alt="Scan UPI QR" style="width:250px;height:250px;margin:20px 0;">
    
    <p style="color:gray;">UPI ID: your-upi@bankname</p>
    <p>After payment, click below to confirm:</p>

    <form action="paymentVerify.php?id=<?php echo $encodedPid; ?>&q=<?php echo $encodedQuant; ?>" method="post">
        <button type="submit" style="padding:10px 20px;background-color:green;color:white;border:none;border-radius:4px;">
            ✅ I Have Paid
        </button>
    </form>
</div>

<?php include_once('./includes/footer.php'); ?>
