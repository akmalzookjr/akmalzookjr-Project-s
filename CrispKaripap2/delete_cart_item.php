<?php
session_start();
include("php/config.php");

// Check if the product id is provided
if (isset($_POST['productId'])) {
    // Sanitize the input
    $productId = mysqli_real_escape_string($con, $_POST['productId']);
    $userId = $_SESSION['id'];

    // Delete the cart item from the database
    $deleteQuery = "DELETE FROM Cart WHERE ProductId = '$productId' AND UserId = '$userId'";
    mysqli_query($con, $deleteQuery);
}
?>
