<?php
session_start();
include("php/config.php");

// Check if the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

// Check if the orderDate parameter is set
if (isset($_GET['orderDate'])) {
    // Sanitize the input
    $orderDate = mysqli_real_escape_string($con, $_GET['orderDate']);
    $userId = $_SESSION['id'];

    // Delete the order from the database
    $deleteOrderQuery = "DELETE FROM completeorders WHERE UserId = '$userId' AND OrderDate = '$orderDate'";
    if (mysqli_query($con, $deleteOrderQuery)) {
        // Order successfully deleted
        echo "Order deleted successfully.";
    } else {
        // Error deleting order
        echo "Error: " . mysqli_error($con);
    }
} else {
    // OrderDate parameter not provided
    echo "Error: OrderDate parameter not provided.";
}
?>