<?php    
include_once('./includes/config.php');
session_start();

// Check if the 'addToCart' form is submitted
if (isset($_POST['addToCart'])) { 
  if (isset($_SESSION['id'])) { // Check if user is logged in
    // Insert the product into the cart table
    $sql22 = "INSERT INTO carts (
        pid,
        uid,
        product,
        price,
        quantity
    ) VALUES(
        {$_SESSION['product_id']}, 
        {$_SESSION['id']}, 
        '{$_SESSION['prod-title']}', 
        '{$_SESSION['prod-price']}', 
        {$_POST['quantity']}
    )";
    // Execute query to add product to cart
    if ($conn->query($sql22) === TRUE) {
        echo "<h4 style='text-align:center;position:absolute;top:100px;left:20%;padding:0;box-shadow: inset 0 -3em 3em rgba(0,0,90,0.1), 0 0 0 2px rgb(255,255,255), 0.3em 0.3em 1em rgba(0,0,0,0.3);'>Product has been Added to your Cart</h4>";
    } else {
        echo "<h4 style='text-align:center;position:absolute;top:100px;left:20%;color:red;'>Error adding to cart!</h4>";
    }
  } else {
    header("Location:login.php?LoginFirst");
    die();
  }
}

// Include header navigation
include_once('./includes/headerNav.php');

// Fetch product data based on product ID from URL or session
if (isset($_GET['id'])) {
  $_SESSION['product_id'] = $_GET['id'];
} else {
  $_GET['id'] = $_SESSION['product_id'];
}

$sql11 = "SELECT * FROM products WHERE product_id='{$_SESSION['product_id']}';";
$result11 = $conn->query($sql11);
$row11 = $result11->fetch_assoc();
$_SESSION['prod-title'] = $row11['product_title'];
$_SESSION['prod-price'] = $row11['product_price'];

// Close database connection
$conn->close();
?>

<head>
    <style>
        .selected_product{
            margin-top:5%;
            display:flex;
            justify-content:center;
        }
        .prod-in{
            position:relative;
            width:60%;
        }
        #image-pr{
            height:80%;
            width:50%;
        }
        .img-magnifier-container {
            position:absolute;
            top:8%;
            left:2%;
            width:90%;
            height:100%;
        }
        .detail-cont-pr{
            position:relative;
            left:49%;
            top:15%;
            width:50%;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
        }

        .img-magnifier-glass {
            position: absolute;
            left:25%;
            opacity:0.1;
            border-radius: 5%;
            cursor: none;
            width: 20px;
            height: 20px;
        }
        .img-magnifier-glass:hover {
            opacity:1;
            border-radius: 10%;
            cursor: none;
            width: 100px;
            height: 100px;
        }
        .price{
            text-align:center;
        }
        .discount{
            text-align:center;
        }
        .description-pr{
            width:100%;
            overflow-y:hidden;
            text-align:center;
            color:grey;
            font-size:medium;
            font-family:cursive;
        }
        .btn-pr{
            display:flex;
            gap:4px;
        }

        .button {
            border: none;
            color: white;
            padding: 16px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            margin: 1px;
            transition-duration: 0.4s;
            cursor: pointer;
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
        }
        .button:hover {
            transform:scale(1.1,1.1);
        }
        .btn2{
            background-color: #E74C3C;
        }
        .btn2:active{
            padding:10px;
        }
        .quantityDiv{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            margin-bottom:20px;
        }
        .section-title{
            margin:0;
            padding:0;
            font-size:14px;
        }
        .addSub{
            height:30px;
            width:30px;
            background:grey;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            background:#C0C0C0;
            font-size:24px;
        }
        .addSub:hover{
            background:#DCDCDC;
        }

        /*responsive for ipad iphone and other */
        @media (max-width: 700px) {
            .prod-in{
                width:100%;
            }
        }
    </style>
    
    <script>
        // Image magnifier
        function magnify(imgID, zoom) {
            var img, glass, w, h, bw;
            img = document.getElementById(imgID);
            glass = document.createElement("DIV");
            glass.setAttribute("class", "img-magnifier-glass");
            img.parentElement.insertBefore(glass, img);
            glass.style.backgroundImage = "url('" + img.src + "')";
            glass.style.backgroundRepeat = "no-repeat";
            glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
            bw = 3;
            w = glass.offsetWidth / 2;
            h = glass.offsetHeight / 2;
            glass.addEventListener("mousemove", moveMagnifier);
            img.addEventListener("mousemove", moveMagnifier);
            glass.addEventListener("touchmove", moveMagnifier);
            img.addEventListener("touchmove", moveMagnifier);
            function moveMagnifier(e) {
                var pos, x, y;
                e.preventDefault();
                pos = getCursorPos(e);
                x = pos.x;
                y = pos.y;
                if (x > img.width - (w / zoom)) {x = img.width - (w / zoom);}
                if (x < w / zoom) {x = w / zoom;}
                if (y > img.height - (h / zoom)) {y = img.height - (h / zoom);}
                if (y < h / zoom) {y = h / zoom;}
                glass.style.left = (x - w) + "px";
                glass.style.top = (y - h) + "px";
                glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
            }
            function getCursorPos(e) {
                var a, x = 0, y = 0;
                e = e || window.event;
                a = img.getBoundingClientRect();
                x = e.pageX - a.left;
                y = e.pageY - a.top;
                x = x - window.pageXOffset;
                y = y - window.pageYOffset;
                return {x : x, y : y};
            }
        }

        function incQuantity() {
            let qtyInput = document.getElementById('quantity');
            let value = parseInt(qtyInput.value);
            if (value < 5) qtyInput.value = value + 1;
        }

        function decQuantity() {
            let qtyInput = document.getElementById('quantity');
            let value = parseInt(qtyInput.value);
            if (value > 1) qtyInput.value = value - 1;
        }
    </script>
</head>
<body>
    <div class="selected_product">
        <div class="prod-in">
            <div class="img-magnifier-container">
                <img id="image-pr" src="admin/upload/<?php echo $row11['product_img']; ?>" alt="Product Image" onload="magnify('image-pr', 3)">
            </div>

            <div class="detail-cont-pr">
                <h5 class="title"><?php echo $row11['product_title']; ?>
                    <p class="date"><?php echo $row11['product_date']; ?></p>
                </h5>
                <p class="description-pr"><?php echo $row11['product_desc']; ?></p>
                <p class="price"><b>Rs.<?php echo $row11['product_price']; ?></b><br>
                    <span class="discount"><strike>5000</strike> -8%</span>
                </p>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <div class="quantityDiv">
                        <h6 class="section-title">Quantity</h6>
                        <span class="addSub" onclick="decQuantity()">-</span>
                        <input id="quantity" name="quantity" type="number" value="1" min="1" max="5" style="width:50px; height:30px; text-align:center;">
                        <span class="addSub" onclick="incQuantity()">+</span>
                    </div>
                    <div class="btn-pr">
                        <?php if (isset($_SESSION['id'])) { ?>
                            <button type="submit" name="addToCart" class="button btn2">Add to Cart</button>
                        <?php } else { ?>
                            <button class="button btn2">
                                <a href="login.php" style="text-decoration:none;color:white;">Login to Add to Cart</a>
                            </button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
