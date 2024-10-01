<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

   // Check user role
   $id = $_SESSION['id'];
   $query = mysqli_query($con,"SELECT * FROM admin WHERE Id='$id' UNION SELECT * FROM users WHERE Id ='$id'");
   $result = mysqli_fetch_assoc($query);
   $role = $result['Role']; 

// Fetch cart items for the logged-in user
$userId = $_SESSION['id'];
$cartItemsQuery = "SELECT Products.Name, Products.Price, Cart.Quantity, Cart.ProductId FROM Cart INNER JOIN Products ON Cart.ProductId = Products.ProductId WHERE Cart.UserId = '$userId'";
$cartItemsResult = mysqli_query($con, $cartItemsQuery);

// Calculate total price for all items
$totalPriceAll = 0;
while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
    $totalPrice = $cartItem['Price'] * $cartItem['Quantity'];
    $totalPriceAll += $totalPrice;
}

// Fetch recent order items for the logged-in user
$userId = $_SESSION['id'];
$orderItemsQuery = "SELECT Products.Name, Products.Price, CompleteOrders.Quantity, CompleteOrders.OrderDate FROM CompleteOrders INNER JOIN Products ON CompleteOrders.ProductId = Products.ProductId WHERE CompleteOrders.UserId = '$userId' AND CompleteOrders.OrderDate = (SELECT MAX(OrderDate) FROM CompleteOrders WHERE UserId = '$userId')";
$orderItemsResult = mysqli_query($con, $orderItemsQuery);

// Calculate total price for all items
$totalPriceAll = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style13s.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->
    <title>Payment Completed</title>
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
                <div class="cartdropdown">
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
                            <a href="order.php">Order</a>
                            <a href="php/logout.php">Logout</a>
                        </div>
                    </button>
                </div>
                <b style="font-size: 14px;"><?php echo $result['Username']; ?></b>
            </nav>
        </div>
    </div>
    
    <div class="payment-completed container">
        <div class="complete-top">
            <h2 style="margin-bottom: 20px; color: #e4e9f7; text-shadow: 0 0 20px white;">Payment Completed</h2>
            <h3  style="margin-bottom: 20px; color: #e4e9f7; text-shadow: 0 0 20px white;">Receipt</h3>
            <div class="complete-box">
                <div class="complete-content">
                    <div class="complete-cart-items">
                        <h4>Items Purchased:</h4>
                        <?php
                        if (mysqli_num_rows($orderItemsResult) > 0) {
                            $totalPriceAll = 0; // Reset total price for all items
                        
                            while ($orderItem = mysqli_fetch_assoc($orderItemsResult)) {
                                $totalPrice = $orderItem['Price'] * $orderItem['Quantity'];
                                $totalPriceAll += $totalPrice;
                                ?>
                                <div class="complete-cart-items-inside">
                                    <?php echo "<p>{$orderItem['Name']} - Quantity: {$orderItem['Quantity']} - Price: RM{$orderItem['Price']}</p>"; ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="complete-cart-total">
                                <p>Total Price for all items: <span style="font-weight:bold">RM<?php echo number_format($totalPriceAll, 2); ?></span></p>
                            </div>
                            <?php
                        } else {
                            echo "<p>No items in the order.</p>";
                        }
                        
                        ?>
                    </div>
                    <div class="done-btn">
                        <button type="button" class="btn" onclick="window.location.href = 'order.php'">View Order History</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function toggleCartDropdown() {
        var dropdownContent = document.getElementById("cartDropdownContent");
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }
    </script>
    <script>
        function checkoutOrder() {
            // Redirect the user to the checkout page
            window.location.href = 'checkout.php';
        }
    </script>
    <script>
        function deleteCartItem(button) {
        // Get the product id from the data-product-id attribute
        var productId = button.getAttribute('data-product-id');

        // AJAX request to delete the cart item
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_cart_item.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Reload the page to reflect the changes
                window.location.reload();
            }
        };
        xhr.send("productId=" + productId);
    }
    </script>
</body>
</html>