<?php
// Include the database connection
include("php/config.php");

// Check if productId is provided
if(isset($_POST['productId'])) {
    // Sanitize the input
    $productId = mysqli_real_escape_string($con, $_POST['productId']);

    // Query to delete the product
    $deleteQuery = "DELETE FROM Products WHERE ProductId = '$productId'";
    
    // Execute the query
    if(mysqli_query($con, $deleteQuery)) {
        // Product deleted successfully
        echo "Product deleted successfully.";
    } else {
        // Error occurred while deleting the product
        echo "Error: " . mysqli_error($con);
    }
} else {
    // ProductId not provided
    echo "Product ID not provided.";
}
?>
