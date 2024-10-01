<?php
session_start();

include("php/config.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['valid']) || $_SESSION['role'] !== 'admin') {
    $response = array(
        'success' => false,
        'message' => 'Unauthorized access'
    );
    echo json_encode($response);
    exit;
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $productId = $_POST['editProductId'];
    $productName = $_POST['editProductName'];
    $productDescription = $_POST['editProductDescription'];
    $productPrice = $_POST['editProductPrice'];

    // Check if file was uploaded without errors
    if (isset($_FILES["editProductImage"]) && $_FILES["editProductImage"]["error"] == UPLOAD_ERR_OK) {
        // Read image data
        $imageData = file_get_contents($_FILES["editProductImage"]["tmp_name"]);

        // Perform update operation in the database along with updating image
        $updateQuery = "UPDATE Products SET Name=?, Description=?, Price=?, Image=? WHERE ProductId=?";
        $stmt = $con->prepare($updateQuery);
        // Bind parameters
        $null = NULL; // Since it's BLOB type
        $stmt->bind_param("sssbi", $productName, $productDescription, $productPrice, $null, $productId);
        // Send the image binary data as a parameter
        $stmt->send_long_data(3, $imageData);

        if ($stmt->execute()) {
            $response = array(
                'success' => true,
                'message' => 'Product updated successfully'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error updating product: ' . $stmt->error
            );
        }
        
        // Close statement
        $stmt->close();
    } else {
        // Perform update operation in the database without updating image
        $updateQuery = "UPDATE Products SET Name=?, Description=?, Price=? WHERE ProductId=?";
        $stmt = $con->prepare($updateQuery);
        // Bind parameters
        $stmt->bind_param("sssi", $productName, $productDescription, $productPrice, $productId);

        if ($stmt->execute()) {
            $response = array(
                'success' => true,
                'message' => 'Product updated successfully'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error updating product: ' . $stmt->error
            );
        }
        
        // Close statement
        $stmt->close();
    }

    // Send JSON response
    echo json_encode($response);
} else {
    // If the request is not a POST request, return error response
    $response = array(
        'success' => false,
        'message' => 'Invalid request method'
    );
    echo json_encode($response);
}
?>
