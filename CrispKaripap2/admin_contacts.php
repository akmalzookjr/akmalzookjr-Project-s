<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: login.php");
   }
   
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
?>
<?php
    // Check if the user is logged in
    if(isset($_SESSION['valid'])) {
        // Check if the user is not an admin
        if($_SESSION['role'] !== "admin") {
            $showCartDropdown = true;
        } else {
            $showCartDropdown = false;
        }
    } else {
        $showCartDropdown = false;
    }

    if(isset($_POST['submit_contact'])) {
        $userId = $_SESSION['id']; // Assuming the user is logged in
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        // Insert the contact information into the database
        $insertQuery = "INSERT INTO contact (UserId, Name, Email, Message) VALUES ('$userId', '$name', '$email', '$message')";
        
        if(mysqli_query($con, $insertQuery)) {
            // Contact information inserted successfully
            echo "<script>alert('Your message has been submitted successfully.');</script>";
        } else {
            // Error occurred while inserting contact information
            echo "<script>alert('Error: Unable to submit your message.');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style7h.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->
    
    <title>Main</title>
</head>
<body><div class="nav">
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
                <div class="cartdropdown" <?php if (!$showCartDropdown) { echo 'style="display:none"'; } ?>>
                    <button class="cartbtn" onclick="toggleCartDropdown()">
                        <span class="cart-icon"><i class="fas fa-shopping-cart"></i></span> 
                        <!-- Display number of items in the cart -->
                        <?php
                        // Fetch total quantity of items in the cart for the logged-in user
                        $userId = $_SESSION['id'];
                        $totalQuantityQuery = "SELECT SUM(Quantity) AS totalQuantity FROM Cart WHERE UserId = '$userId'";
                        $totalQuantityResult = mysqli_query($con, $totalQuantityQuery);
                        $totalQuantityData = mysqli_fetch_assoc($totalQuantityResult);
                        $totalQuantity = $totalQuantityData['totalQuantity'];

                        // Display cart icon with total quantity
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
                            <a href="<?php echo ($role === "admin") ? 'admin_orders.php' : 'order.php'; ?>">Order</a>
                            <?php if($_SESSION['role'] === "admin"): ?>
                                <a href="admin_contacts.php">Contact</a>
                            <?php endif; ?>
                            <a href="php/logout.php">Logout</a>
                        </div>
                    </button>
                </div>
                <b style="font-size: 14px;"><?php echo $result['Username']; ?></b>
            </nav>
        </div>
    </div>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 25px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr {
            background-color: #ddd;
            transition: all 0.5s ease;
        }

        tr:hover {
            background-color: #ddd;
            transform: scale(1.01);
        }
    </style>
    <div class="home-page section" id="home">
        <div class="container1">
        <h1>Admin Contacts</h1>
        <table border="1">
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
            </tr>
            <?php
            // Fetch all contact data from the database
            $query = "SELECT * FROM contact";
            $result = mysqli_query($con, $query);

            // Check if there are any contacts
            if(mysqli_num_rows($result) > 0) {
                // Contacts exist, display them in a table
                while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row['UserId']; ?></td>
                        <td><?php echo $row['Name']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td><?php echo $row['Message']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                // No contacts found in the database
                echo "<tr><td colspan='4'>No contacts found.</td></tr>";
            }
            ?>
        </table>
        </div>
    </div>
</body>
</html>
