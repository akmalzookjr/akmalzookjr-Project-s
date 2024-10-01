<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

// Fetch cart items for the logged-in user
$userId = $_SESSION['id'];
$cartItemsQuery = "SELECT * FROM Cart WHERE UserId = '$userId'";
$cartItemsResult = mysqli_query($con, $cartItemsQuery);

if (!$cartItemsResult) {
    // Error handling for cart items query
    echo "Error fetching cart items: " . mysqli_error($con);
} else {
    // Check if cart items exist
    if (mysqli_num_rows($cartItemsResult) > 0) {
        // Loop through cart items and transfer each one to completeorders table
        while ($cartItem = mysqli_fetch_assoc($cartItemsResult)) {
            $productId = $cartItem['ProductId'];
            $quantity = $cartItem['Quantity'];
            $orderDate = date("Y-m-d H:i:s"); // Current date and time

            // Insert into completeorders table without specifying OrderId
            $insertCompleteOrdersQuery = "INSERT INTO completeorders (UserId, ProductId, Quantity, OrderDate) VALUES ('$userId', '$productId', '$quantity', '$orderDate')";
            $insertCompleteOrdersResult = mysqli_query($con, $insertCompleteOrdersQuery);

            // Insert into orderdetails table
            $insertOrderDetailsQuery = "INSERT INTO orderdetails (UserId, ProductId, Quantity, OrderDate) VALUES ('$userId', '$productId', '$quantity', '$orderDate')";
            $insertOrderDetailsResult = mysqli_query($con, $insertOrderDetailsQuery);
            
            if (!$insertCompleteOrdersResult && !$insertOrderDetailsResult) {
                // Error handling for insert query
                echo "Error inserting into completeorders table: " . mysqli_error($con);
            } else {
                // Delete from cart table
                $deleteQuery = "DELETE FROM Cart WHERE UserId = '$userId' AND ProductId = '$productId'";
                $deleteResult = mysqli_query($con, $deleteQuery);
                if (!$deleteResult) {
                    // Error handling for delete query
                    echo "Error deleting from cart table: " . mysqli_error($con);
                }
            }
        }
    } else {
        echo "No cart items found for user ID: $userId<br>";
    }
}

// Close database connection
mysqli_close($con);

// Redirect to payment_completed.php
header("Location: payment_completed.php");
exit();
?>
