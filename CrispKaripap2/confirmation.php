<?php
// Include database configuration
include("php/config.php");

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID
$userId = $_SESSION['id'];

// Retrieve user information from the database
$query = "SELECT * FROM users WHERE Id='$userId'";
$result = mysqli_query($con, $query);
$userData = mysqli_fetch_assoc($result);

// Retrieve order details from the database
$orderQuery = "SELECT Products.Name, Products.Price, Cart.Quantity 
               FROM Cart 
               INNER JOIN Products ON Cart.ProductId = Products.ProductId 
               WHERE Cart.UserId = '$userId'";
$orderResult = mysqli_query($con, $orderQuery);

// Calculate total price
$totalPrice = 0;
while ($row = mysqli_fetch_assoc($orderResult)) {
    $totalPrice += $row['Price'] * $row['Quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    
    <h2>User Details</h2>
    <p>Name: <?php echo $userData['Username']; ?></p>
    <p>Email: <?php echo $userData['Email']; ?></p>
    <p>Age: <?php echo $userData['Age']; ?></p>
    
    <h2>Order Details</h2>
    <table border="1">
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
        </tr>
        <?php
        // Display order details
        $orderResult = mysqli_query($con, $orderQuery);
        while ($row = mysqli_fetch_assoc($orderResult)) {
            $productName = $row['Name'];
            $price = $row['Price'];
            $quantity = $row['Quantity'];
            $totalProductPrice = $price * $quantity;
            ?>
            <tr>
                <td><?php echo $productName; ?></td>
                <td><?php echo $price; ?></td>
                <td><?php echo $quantity; ?></td>
                <td><?php echo $totalProductPrice; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>

    <h2>Total Price: <?php echo $totalPrice; ?></h2>
</body>
</html>