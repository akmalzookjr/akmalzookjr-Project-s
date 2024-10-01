<?php 
    session_start();

    // Include the database configuration file
    include("php/config.php");

    // Check if the user is logged in
    if (!isset($_SESSION['valid'])) {
        header("Location: login.php");
        exit();
    }

    // Fetch cart items for the logged-in user
    $userId = $_SESSION['id'];
    $cartItemsQuery = "SELECT Products.Name, Products.Price, Cart.Quantity, Cart.ProductId FROM Cart INNER JOIN Products ON Cart.ProductId = Products.ProductId WHERE Cart.UserId = '$userId'";
    $cartItemsResult = mysqli_query($con, $cartItemsQuery);

    // Check user role
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

    // Check if the cart is empty
    $userId = $_SESSION['id'];
    $cartQuery = "SELECT COUNT(*) AS itemCount FROM Cart WHERE UserId = '$userId'";
    $cartResult = mysqli_query($con, $cartQuery);
    $cartData = mysqli_fetch_assoc($cartResult);
    $itemCount = $cartData['itemCount'];

    if ($itemCount == 0) {
        echo "<script>alert('Your cart is empty! Please add some items to proceed with the checkout.');</script>";
        echo "<script>window.location.href = 'shop.php';</script>";
        exit();
    }

    // Calculate total price for all items
    $totalPriceAll = 0;
    while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
        $totalPrice = $cartItem['Price'] * $cartItem['Quantity'];
        $totalPriceAll += $totalPrice;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style13s.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->
    <title>Checkout</title>
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
                            <a href="order.php">Order</a>
                            <a href="php/logout.php">Logout</a>
                        </div>
                    </button>
                </div>
                <b style="font-size: 14px;"><?php echo $result['Username']; ?></b>
            </nav>
        </div>
    </div>
    <div class="checkout-content">
        <div class="cart-items">
            <h2>Cart Items</h2>
            <?php
            // Fetch cart items for the user from the database
                $cartItemsQuery = "SELECT Products.Name, Products.Price, Cart.Quantity FROM Cart INNER JOIN Products ON Cart.ProductId = Products.ProductId WHERE Cart.UserId = '$userId'";
                $cartItemsResult = mysqli_query($con, $cartItemsQuery);

                // Reset the pointer of $cartItemsResult
                mysqli_data_seek($cartItemsResult, 0);

                // Check if there are cart items
                if (mysqli_num_rows($cartItemsResult) > 0) {
                    // Display cart items
                    while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
                        ?>
                        <div class="cart-item-middle">
                            <p><?php echo $cartItem['Name']; ?></p>
                            <p>Quantity: <?php echo $cartItem['Quantity']; ?></p>
                            <p>Price: RM<?php echo $cartItem['Price']; ?></p>
                            <!-- Calculate and display total price for the item -->
                            <p>Total Price: RM<?php echo number_format($cartItem['Price'] * $cartItem['Quantity'], 2); ?></p>
                        </div>
                        <?php
                    }
                } else {
                    // If no items in cart, display a message
                    echo "<p>No items in the cart.</p>";
                }
            ?>
            <!-- Display Total Price for all items -->
            <div class="cart-item-middle" style="background-color: lightgreen">Total Price for all items: <span style="font-weight: bold">RM<?php echo number_format($totalPriceAll, 2); ?></span></div>
        </div>
        <!-- Payment form -->
        <div class="payment-details">
            <h2>Payment Details</h2>
            <form id="paymentForm" action="complete_order.php" method="post" class="payment-form" onsubmit="return validatePaymentForm()">
                <!-- Payment form fields -->
                <div class="form-field">
                    <label for="cardNumber">Card Number:</label>
                    <input type="text" id="cardNumber" name="cardNumber" pattern="[0-9\s]{13,19}" 
                    inputmode="numeric" autocomplete="cc-number" maxlength="19" 
                    placeholder="xxxx xxxx xxxx xxxx" required>
                </div>
                <div class="form-field">
                    <label for="expiryDate">Expiry Date:</label>
                    <input type="text" id="expiryDate" name="expiryDate" size="6" maxlength="5" placeholder="MM/YY" required>
                </div>
                <div class="form-field">
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" placeholder="000" required>
                </div>
                <div class="done-btn">
                    <button type="submit" class="btn">Complete Order</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    
</script>
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
<script>
    // Function to format card number with spacing after every four digits
    function formatCardNumber() {
        var cardNumberInput = document.getElementById("cardNumber");
        var cardNumberValue = cardNumberInput.value.replace(/\D/g, ''); // Remove non-numeric characters
        var formattedCardNumber = "";

        for (var i = 0; i < cardNumberValue.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedCardNumber += " "; // Add a space after every four digits
            }
            formattedCardNumber += cardNumberValue[i];
        }

        cardNumberInput.value = formattedCardNumber;
    }

    // Attach the formatCardNumber function to the input's keyup event
    document.getElementById("cardNumber").addEventListener("keyup", formatCardNumber);
</script>
<script>
    // Function to format expiry date as MM/YY
    function formatExpiryDate() {
        var expiryDateInput = document.getElementById("expiryDate");
        var expiryDateValue = expiryDateInput.value.replace(/\D/g, ''); // Remove non-numeric characters
        var formattedExpiryDate = "";

        if (expiryDateValue.length >= 3) {
            formattedExpiryDate = expiryDateValue.substring(0, 2) + "/" + expiryDateValue.substring(2); // Format as MM/YY
        } else {
            formattedExpiryDate = expiryDateValue;
        }

        expiryDateInput.value = formattedExpiryDate;
    }

    // Attach the formatExpiryDate function to the input's keyup event
    document.getElementById("expiryDate").addEventListener("keyup", formatExpiryDate);
</script>
<script>
    // Function to format CVV with a maximum length of 3 digits
    function formatCVV() {
        var cvvInput = document.getElementById("cvv");
        var cvvValue = cvvInput.value.replace(/\D/g, ''); // Remove non-numeric characters
        var formattedCVV = cvvValue.substring(0, 3); // Get the first 3 digits

        cvvInput.value = formattedCVV;
    }

    // Attach the formatCVV function to the input's keyup event
    document.getElementById("cvv").addEventListener("keyup", formatCVV);
</script>
<script>
    function completeOrder() {
    // Send AJAX request to complete_order.php
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "complete_order.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Redirect to payment_completed.php after completion
            window.location.href = 'payment_completed.php';
        }
    };
    xhr.send();
    alert("Complete Order button clicked"); // Add this line for debugging
}
</script>
<script>
    function validatePaymentForm() {
        var cardNumber = document.getElementById("cardNumber").value;
        var expiryDate = document.getElementById("expiryDate").value;
        var cvv = document.getElementById("cvv").value;

        // Validate card number (simple validation, can be improved)
        if (!isValidCardNumber(cardNumber)) {
            alert("Please enter a valid card number.");
            return false;
        }

        // Validate expiry date (simple validation, can be improved)
        if (!isValidExpiryDate(expiryDate)) {
            alert("Please enter a valid expiry date in MM/YY format.");
            return false;
        }

        // Validate CVV (simple validation, can be improved)
        if (!isValidCVV(cvv)) {
            alert("Please enter a valid CVV.");
            return false;
        }

        return true; // Form is valid, proceed with submission
    }

    // Function to validate card number (simple validation, can be improved)
    function isValidCardNumber(cardNumber) {
        var cardNumberRegex = /^[0-9\s]{13,19}$/;
        return cardNumberRegex.test(cardNumber);
    }

    // Function to validate expiry date (simple validation, can be improved)
    function isValidExpiryDate(expiryDate) {
        var expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/; // MM/YY format
        return expiryDateRegex.test(expiryDate);
    }

    // Function to validate CVV (simple validation, can be improved)
    function isValidCVV(cvv) {
        var cvvRegex = /^[0-9]{3}$/; // 3 digits
        return cvvRegex.test(cvv);
    }
</script>
</body>
</html>