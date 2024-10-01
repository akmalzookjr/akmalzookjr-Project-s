<?php
session_start();
include("php/config.php");

// Check if the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit(); // Stop further execution
}

// Check if productId and quantity are provided
if (isset($_POST['productId']) && isset($_POST['quantity'])) {
    // Sanitize inputs to prevent SQL injection
    $productId = mysqli_real_escape_string($con, $_POST['productId']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);

    // Check if the product already exists in the cart
    $userId = $_SESSION['id'];
    $checkQuery = "SELECT * FROM Cart WHERE UserId = '$userId' AND ProductId = '$productId'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Product already exists in the cart, update the quantity
        $cartItem = mysqli_fetch_assoc($checkResult);
        $newQuantity = $cartItem['Quantity'] + $quantity;

        // Update the quantity in the Cart table
        $updateQuery = "UPDATE Cart SET Quantity = '$newQuantity' WHERE UserId = '$userId' AND ProductId = '$productId'";
        if (mysqli_query($con, $updateQuery)) {
            // Quantity updated successfully
            header("Location: shop.php");
            exit();
        } else {
            // Error occurred while updating quantity
            echo "Error: " . mysqli_error($con);
        }
    } else {
        // Product doesn't exist in the cart, insert new cart item
        $insertQuery = "INSERT INTO Cart (UserId, ProductId, Quantity) VALUES ('$userId', '$productId', '$quantity')";
        if (mysqli_query($con, $insertQuery)) {
            // Product added to cart successfully
            header("Location: shop.php");
            exit();
        } else {
            // Error occurred while adding product to cart
            echo "Error: " . mysqli_error($con);
        }
    }
} else {
    // Missing productId or quantity
    echo "Invalid request.";
}
?>
