<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];
$query = mysqli_query($con,"SELECT * FROM admin WHERE Id='$id'");
$result = mysqli_fetch_assoc($query);
$role = $result['Role']; 

// Set profile link based on user role
if($role === "admin") {
    $profile_link = "profile-admin.php";
} else {
    $profile_link = "profile.php";
}

if($role !== "admin") {
    header("Location: shop.php"); // Redirect non-admin users
    exit();
}

// Fetch all distinct user IDs
$userQuery = "SELECT DISTINCT UserId FROM completeorders";
$userResult = mysqli_query($con, $userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style13s.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->

    <title>Admin Order Management</title>
    <style>
        /* CSS styles */
        body {
            background: #e4e9f7;
            background-size: cover;
            background-image: url('Others/HD-Karipap.png');
            background-position:center;
            background-size: 1920px;
            background-repeat: no-repeat;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .ao-container-order{
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            width: 100%;
            height: 100%;
        }   
        .ao-container-order h1{        
            margin-top: 100px;
            color: white;
            /* background: red; */
        }
        .ao-container-order .ao-content{
            /* background: blue; */
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .ao-container-order .ao-content .ao-inside{
            /* background: green; */
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 40px;
            justify-content: center;
            margin-top: 20px;
            border:
        }
        .ao-user-orders{
            width: 45%;
            gap: 20px;
            background:white;
            border-radius: 25px;
            box-shadow: 0 0 10px 0 black;
            margin: 0 20px 0 20px;
            display:flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 50%;
            padding-bottom: 20px;
            transition: transform 0.5s ease;
        }

        .ao-user-orders:hover{
            transform: scale(1.1);
        }

        .ao-order-box {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 25px;
            height: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 10px 0 black;
            width: 90%;
        }
        .ao-order-box-main{
            box-shadow: 0 0 10px 0 black;
            border-radius: 25px;
            padding: 5px;
            transition: transform 0.5s ease;
        }
        .ao-order-box-main:hover{
            transform: scale(1.1);
        }

        .ao-order-box-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-top: 10px;
        }

        .ao-order-box-details p {
            margin: 5px 0;
        }
    </style>
</head>
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
                <?php
                // Check if the user is logged in
                if(isset($_SESSION['valid'])) {
                    // Check if the user is not an admin
                    if($_SESSION['role'] !== "admin") {
                        $showCartDropdown = true;
                    } else {
                        $showCartDropdown = false;
                    }
                } else {
                    $showCartDropdown = false;
                }
            ?>
                <div class="cartdropdown" <?php if (!$showCartDropdown) { echo 'style="display:none"'; } ?>>
                    <button class="cartbtn" onclick="toggleCartDropdown()">
                        <span class="cart-icon"><i class="fas fa-shopping-cart"></i></span> 
                        <!-- Display number of items in the cart -->
                        <?php
                        // Fetch total quantity of items in the cart for the logged-in user
                        $userId = $_SESSION['id'];
                        $totalQuantityQuery = "SELECT SUM(Quantity) AS totalQuantity FROM Cart WHERE UserId = '$userId'";
                        $totalQuantityResult = mysqli_query($con, $totalQuantityQuery);
                        $totalQuantityData = mysqli_fetch_assoc($totalQuantityResult);
                        $totalQuantity = $totalQuantityData['totalQuantity'];

                        // Display cart icon with total quantity
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
    <!-- Container -->
    <div class="container">
        <div class="ao-container-order">
            <h1>Admin Order Management</h1>
            <div class="ao-content">
            <?php
            if(mysqli_num_rows($userResult) > 0) {
                ?>
                <div class="ao-inside">
                    <?php
                    while($user = mysqli_fetch_assoc($userResult)) {
                        $userId = $user['UserId'];
                        // Fetch user information
                        $userQueryInner = "SELECT Username FROM users WHERE Id = '$userId'";
                        $userResultInner = mysqli_query($con, $userQueryInner);
                        $userData = mysqli_fetch_assoc($userResultInner);
                        $username = $userData['Username'];

                        // Fetch orders for this user sorted by order date
                        $orderQuery = "SELECT DISTINCT OrderDate FROM orderdetails WHERE UserId = '$userId' ORDER BY OrderDate ASC";
                        $orderResult = mysqli_query($con, $orderQuery);

                        if(mysqli_num_rows($orderResult) > 0) {
                            ?>
                            <div class="ao-user-orders">
                                <h2 style="margin-top: 10px;">Orders for User: <?php echo $username; ?><hr></h2>
                                <?php
                                $orderNumber = 1;
                                while($order = mysqli_fetch_assoc($orderResult)) {
                                    $orderDate = $order['OrderDate'];
                                    $orderDateId = str_replace(" ", "-", $orderDate); // Create unique ID for each order date
                                    
                                    // Output order date with clickable functionality
                                    echo "<h3 onclick=\"toggleOrderDetails('$orderDateId')\">Order {$orderNumber}: <br><p style='font-size: 13px'>{$orderDate}</p></h3>";
                                    // Output order details with unique ID
                                    echo "<div id=\"$orderDateId-details\" style=\"display: none;\">";

                                    // Fetch orders for this user on this date
                                    $orderDetailsQuery = "SELECT * FROM orderdetails WHERE UserId = '$userId' AND OrderDate = '$orderDate'";
                                    $orderDetailsResult = mysqli_query($con, $orderDetailsQuery);

                                    if(mysqli_num_rows($orderDetailsResult) > 0) {
                                        $totalPriceAll = 0; // Variable to store the total price for this date
                                        echo "<div class='ao-order-box-main'>";
                                        
                                        while($orderDetail = mysqli_fetch_assoc($orderDetailsResult)) {
                                            echo "<div class='ao-order-box-details'>";
                                            // Fetch product information
                                            $productId = $orderDetail['ProductId'];
                                            $quantity = $orderDetail['Quantity'];
                                            
                                            $productQuery = "SELECT Name, Price FROM products WHERE ProductId = '$productId'";
                                            $productResult = mysqli_query($con, $productQuery);
                                            $productData = mysqli_fetch_assoc($productResult);
                                            $productName = $productData['Name'];
                                            $productPrice = $productData['Price'];
                                            $totalPrice = $productPrice * $quantity; // Calculate total price for this product
                                            $totalPriceAll += $totalPrice; // Add to the total price for this date
                                            $formattotalPriceAll = number_format($totalPriceAll, 2);

                                            echo "<p>Product: {$productName} - Quantity: {$quantity} (RM{$totalPrice})</p>";
                                            echo "</div>";
                                        }
                                        
                                        // Display total price for this date
                                        echo "<hr style='margin-top: 20px'><p>Total Price for this date: <b>RM{$formattotalPriceAll}</b></p>";
                                        echo "</div>";
                                    } else {
                                        echo "<p>No orders found for this date.</p>";
                                    }

                                    echo "</div>"; // Close order-box div
                                    $orderNumber++;
                                }
                                
                                ?>
                                </div>
                            <?php
                        } else {
                            echo "<div class='ao-user-orders'>";
                            echo "<p><b>No orders found for User: $username.</b></p>";
                            echo "</div>";
                        }
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
    </div>
    <script>
    function toggleCartDropdown() {
        var dropdownContent = document.getElementById("cartDropdownContent");
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }

    function deleteCartItem(button) {
        var productId = button.getAttribute("data-product-id");
        // AJAX call to delete the item from the cart
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                window.location.reload(); // Reload the page after successful deletion
            }
        };
        xhr.open("GET", "php/deleteCartItem.php?productId=" + productId, true);
        xhr.send();
    }

    function checkoutOrder() {
        // Redirect to checkout page
        window.location.href = "checkout.php";
    }

    function toggleOrderDetails(orderDateId) {
        var orderDetails = document.getElementById(orderDateId + "-details");
        if (orderDetails.style.display === "none") {
            orderDetails.style.display = "block";
        } else {
            orderDetails.style.display = "none";
        }
    }
    </script>
</body>
</html>
