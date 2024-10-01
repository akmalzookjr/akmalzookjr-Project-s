<?php
// Include database configuration
include("php/config.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve payment details from the form
    $cardNumber = $_POST["cardNumber"];
    $expiryDate = $_POST["expiryDate"];
    $cvv = $_POST["cvv"];

    // You can implement payment processing logic here
    // For demonstration purposes, let's assume the payment is successful
    // and complete the order by clearing the cart

    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['valid'])) {
        header("Location: login.php");
        exit();
    }

    // Clear the cart after successful payment
    $userId = $_SESSION['id'];
    $clearCartQuery = "DELETE FROM Cart WHERE UserId = '$userId'";
    mysqli_query($con, $clearCartQuery);

    // Redirect the user to a confirmation page
    header("Location: confirmation.php");
    exit();
} else {
    // If the form is not submitted, redirect the user back to the checkout page
    header("Location: checkout.php");
    exit();
}
?>
