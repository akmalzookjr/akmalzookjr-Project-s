<?php
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: login.php");
    exit(); // Add exit after redirection to prevent further execution
}

// Check if the form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];

    // Check if file was uploaded without errors
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        // Read image data
        $imageData = file_get_contents($_FILES["image"]["tmp_name"]);

        // Insert product into database along with binary image data
        $insertQuery = "INSERT INTO Products (Name, Description, Price, Image) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($insertQuery);
        // Bind parameters
        $null = NULL; // Since it's BLOB type
        $stmt->bind_param("sssb", $productName, $productDescription, $productPrice, $null);
        // Send the image binary data as a parameter
        $stmt->send_long_data(3, $imageData);

        if ($stmt->execute()) {
            $response = array(
                "success" => true,
                "message" => "New product added successfully."
            );
        } else {
            $response = array(
                "success" => false,
                "message" => "Error: " . $stmt->error
            );
        }
        
        // Close statement
        $stmt->close();
    } else {
        $response = array(
            "success" => false,
            "message" => "Error: No file was uploaded or file upload error occurred."
        );
    }
    
    // Return JSON response
    echo json_encode($response);
}
?>
