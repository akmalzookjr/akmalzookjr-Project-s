<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];
$query = mysqli_query($con,"SELECT * FROM admin WHERE Id='$id' UNION SELECT * FROM users WHERE Id ='$id'");
$result = mysqli_fetch_assoc($query);
$role = $result['Role']; 

// Set profile link based on user role
if($role === "admin") {
    $profile_link = "profile-admin.php";
} else {
    $profile_link = "profile.php";
}

// Fetch order history for the logged-in user from completeorders table, grouped by order date
$orderQuery = "SELECT DISTINCT OrderDate FROM completeorders WHERE UserId = '$id' GROUP BY OrderDate";
$orderResult = mysqli_query($con, $orderQuery);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style13s.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->

    <title>Order History</title>
</head>
<style>
    body{
        background-size: cover;
        display: flex;
        flex-direction: column;
        background-image: url('Others/HD-Karipap.png');
        background-position:center;
        background-size: 1920px;
        background-repeat: no-repeat;
        
    }
    </style>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="main.php#home"><img src="Others\Crisp Karipap_transparent.png" alt="CrispKaripap"></a> </p>
        </div>

        <div class="right-links">
            <nav class="navigation">
                <a href="main.php#home">Home</a>
                <a href="main.php#product">Product</a>
                <a href="main.php#about">About</a>
                <a href="main.php#contact">Contact</a>
                <a href="shop.php">Shop</a>
                <div class="cartdropdown">
                    <button class="cartbtn" onclick="toggleCartDropdown()">
                        <span class="cart-icon"><i class="fas fa-shopping-cart"></i></span> 
                        <!-- Display number of items in the cart -->
                        <?php
                        // Fetch cart items count for the logged-in user
                        $userId = $_SESSION['id'];
                        $totalQuantityQuery = "SELECT SUM(Quantity) AS totalQuantity FROM Cart WHERE UserId = '$userId'";
                        $totalQuantityResult = mysqli_query($con, $totalQuantityQuery);
                        $totalQuantityData = mysqli_fetch_assoc($totalQuantityResult);
                        $totalQuantity = $totalQuantityData['totalQuantity'];

                        // Display cart icon with item count
                        echo "Cart (" . ($totalQuantity > 0 ? $totalQuantity : 0) . ")";
                        ?>
                    </button>
                    <div class="dropdown-content" id="cartDropdownContent">
                        <?php
                        // Fetch cart items for the logged-in user
                        $cartItemsQuery = "SELECT Products.Name, Products.Price, Cart.Quantity, Cart.ProductId FROM Cart INNER JOIN Products ON Cart.ProductId = Products.ProductId WHERE Cart.UserId = '$userId'";
                        $cartItemsResult = mysqli_query($con, $cartItemsQuery);

                        // Check if there are cart items
                        if (mysqli_num_rows($cartItemsResult) > 0) {
                            $totalPriceAll = 0; // Variable to store the total price of all items in the cart

                            // Loop through each cart item and display
                            while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
                                $totalPrice = $cartItem['Price'] * $cartItem['Quantity']; // Calculate total price for each product
                                $totalPriceAll += $totalPrice; // Add to the total price of all items
                                ?>
                                <div class="cart-item">
                                    <p><span style="font-weight: bold;"><?php echo $cartItem['Name']; ?></span> - Quantity: <?php echo $cartItem['Quantity']; ?> - Total Price: RM<?php echo $totalPrice; ?></p>
                                    <!-- Delete button with product id -->
                                    <div class="trash-icon">
                                        <button class="delete-btn" data-product-id="<?php echo $cartItem['ProductId']; ?>" onclick="deleteCartItem(this)"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <?php
                            }
                            // Display total price for all items
                            ?>
                            <div class="cart-total">
                            <p>Total Price for all items: <span style="font-weight:bold">RM<?php echo $totalPriceAll; ?></span></p>
                            </div>
                            <?php
                        } else {
                            echo "<p>No items in the cart.</p>";
                        }
                        ?>
                        <button class="checkout-btn" onclick="checkoutOrder()">Checkout</button>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn"><span class="profile-icon"><i class="fas fa-user"></i></span> 
                        <div class="dropdown-content">
                            <a href="<?php echo $profile_link; ?>">Profile</a>
                            <a href="<?php echo ($role === "admin") ? 'admin_orders.php' : 'order.php'; ?>">Order</a>
                            <a href="php/logout.php">Logout</a>
                        </div>
                    </button>
                </div>
                <b style="font-size: 14px;"><?php echo $result['Username']; ?></b>
            </nav>
        </div>
    </div>
    <div class="user-order container">
        <div class="container-order">
            <h1  style="margin-bottom: 20px; color: #e4e9f7; text-shadow: 0 0 20px white;">Order History</h1>
            <?php
            $orderNumber = 1;
            if(mysqli_num_rows($orderResult) > 0) {
            ?>
            <div class="order-box">
                <?php
                while($order = mysqli_fetch_assoc($orderResult)) {
                    echo "<div class='order-box-details'>";
                    echo "<h2>Order {$orderNumber}: <br><p style='font-size: 13px'>{$order['OrderDate']}</p></h2>";
                    
                    // Fetch order details
                    $orderDate = $order['OrderDate'];
                    $orderDetailsQuery = "SELECT p.Name AS ProductName, SUM(od.Quantity) AS Quantity, SUM(od.Quantity * p.Price) AS Subtotal FROM products p INNER JOIN completeorders od ON p.ProductId = od.ProductId WHERE od.UserId = '$id' AND od.OrderDate = '$orderDate' GROUP BY p.Name";
                    $orderDetailsResult = mysqli_query($con, $orderDetailsQuery);
                    if(mysqli_num_rows($orderDetailsResult) > 0) {
                        while($orderDetail = mysqli_fetch_assoc($orderDetailsResult)) {
                            echo "{$orderDetail['ProductName']} - Quantity: {$orderDetail['Quantity']} - Subtotal: RM{$orderDetail['Subtotal']}</br>";
                        }
                        // Calculate total price for the order
                        $totalOrderPriceQuery = "SELECT SUM(od.Quantity * p.Price) AS TotalPrice FROM products p INNER JOIN completeorders od ON p.ProductId = od.ProductId WHERE od.UserId = '$id' AND od.OrderDate = '$orderDate'";
                        $totalOrderPriceResult = mysqli_query($con, $totalOrderPriceQuery);
                        $totalOrderPriceData = mysqli_fetch_assoc($totalOrderPriceResult);
                        $totalOrderPrice = $totalOrderPriceData['TotalPrice'];
                        echo "<div class='order-gap'><hr><p>Total Price for Order: <b>RM{$totalOrderPrice}</b></p></div>";
                    } else {
                        echo "<p>No order details found for this order.</p>";
                    }
                    echo "</div>";
                    $orderNumber++;
                }
                ?>
            </div>
            <?php
            } else {
                echo "<p>No orders found.</p>";
            }
            ?>
        </div>
    </div>
    <script>
    function deleteOrder(button) {
        var orderDate = button.getAttribute('data-order-date');
        // AJAX call to delete the order
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Reload the page after successful deletion
                window.location.reload();
            }
        };
        xhr.open('GET', 'deleteOrder.php?orderDate=' + orderDate, true);
        xhr.send();
    }

    function toggleCartDropdown() {
        var dropdownContent = document.getElementById("cartDropdownContent");
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }

    function checkoutOrder() {
        // Redirect the user to the checkout page
        window.location.href = 'checkout.php';
    }
</script>
</body>
</html>
