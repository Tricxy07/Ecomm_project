<?php
session_start();
include_once('./includes/headerNav.php');
include_once('./includes/config.php');

if (!isset($_SESSION['id'])) {
    header("location:index.php?error=unauthorized");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart - Electric Shop</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
    }

    th {
      background-color: #f2f2f2;
    }

    .text-end {
      text-align: right;
      margin-top: 20px;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      color: white;
      cursor: pointer;
    }

    .btn-danger {
      background-color: red;
    }

    .btn-upi {
      background-color: orangered;
    }

    .btn-continue {
      background-color: #11C9B6;
    }

    .btn-cod {
      background-color: green;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Cart</h1>
  <?php
  $total = 0;
  $pidArray = [];
  $quantArray = [];

  $sql = "SELECT * FROM carts WHERE uid={$_SESSION['id']} AND status='active'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
  ?>
    <table>
      <thead>
        <tr>
          <th>Sn</th>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sn = 1;
      while ($row = $result->fetch_assoc()) {
          $pidArray[] = $row['pid'];
          $quantArray[] = $row['quantity'];
          $itemTotal = $row['price'] * $row['quantity'];
          $total += $itemTotal;
      ?>
        <tr>
          <td><?php echo $sn++; ?></td>
          <td><?php echo $row['product']; ?></td>
          <td>₹<?php echo $row['price']; ?></td>
          <td><?php echo $row['quantity']; ?></td>
          <td>₹<?php echo $itemTotal; ?></td>
          <td>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?pid=<?php echo $row['pid']; ?>&q=<?php echo $row['quantity']; ?>" method="post">
              <button name="delete" type="submit" class="btn btn-danger">Remove</button>
            </form>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>

    <?php
      $encodedPid = urlencode(serialize($pidArray));
      $encodedQuant = urlencode(serialize($quantArray));
    ?>

    <div class="text-end">
      <h4>Total (including ₹50 shipping): ₹<?php echo $total + 50; ?></h4>
    </div>

    <!-- Continue Shopping -->
    <div class="text-end">
      <a href="./index.php" class="btn btn-continue">Continue Shopping</a>
    </div>

    <!-- UPI QR Option -->
    <div class="text-end" style="margin-top: 20px;">
      <form action="upiPayment.php" method="get">
        <input type="hidden" name="id" value="<?php echo $encodedPid; ?>">
        <input type="hidden" name="q" value="<?php echo $encodedQuant; ?>">
        <p><strong>Pay using UPI:</strong> siddharth@upi</p>
        <button type="submit" class="btn btn-upi">Pay with UPI (QR)</button>
      </form>
    </div>

    <!-- Pay on Delivery Option -->
    <div class="text-end" style="margin-top: 20px;">
      <form action="codPayment.php" method="post">
        <input type="hidden" name="id" value="<?php echo $encodedPid; ?>">
        <input type="hidden" name="q" value="<?php echo $encodedQuant; ?>">
        <button type="submit" class="btn btn-cod">Pay on Delivery</button>
      </form>
    </div>

  <?php } else {
    echo "<p>No items in cart.</p>";
  } ?>

</div>

<?php
if (isset($_POST['delete'])) {
    $sql = "DELETE FROM carts WHERE pid={$_GET['pid']} AND quantity={$_GET['q']} LIMIT 1";
    $conn->query($sql);
    header("Location:cart.php?itemRemovedSuccessfully");
}
include_once('./includes/footer.php');
?>

</body>
</html>
