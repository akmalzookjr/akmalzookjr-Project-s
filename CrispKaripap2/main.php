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
<body>
<div class="nav">
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
    <div class="home-page section" id="home">
        <div class="container1">
            <div class="content">
                <div class="head-title">
                    <h1>Welcome To CrispKaripap</h1>
                    <p>“Where Tradition Meets Crispy Delight. Taste the Authentic Flavors of Malaysia in Every Bite!”</p>
                    <div class="crisp-story">
                        <img src="Others/karipap0.jpeg">
                        <p><a>Welcome to CrispKaripap,<br></a>
                            Where we're on a mission to share the authentic 
                            flavors of Malaysia with the world. Founded with 
                            a passion for tradition and taste, our family-run 
                            business crafts each karipap with care, using 
                            time-honored recipes passed down through generations. 
                            From our kitchen to your doorstep, we're dedicated to 
                            delivering crispy, savory karipaps that bring joy to 
                            every bite. Thank you for choosing CrispKaripap—we can't 
                            wait to share the taste of Malaysia with you.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="product-page section" id="product">
        <div class="container1">
            <div class="content">
                <div class="head-title">
                    <h1>Product</h1>
                    <div class="product-content">
                        <div class="product-box">
                            <p style="font-weight: bold; font-size: 23px; text-align: center">Karipap Kentang</p>
                            <img src="Others/Kentang.png" alt="Karipap-Kentang">
                            <p><a>Karipap Kentang, </a>a cherished Malaysian snack, boasts a delightful mix of crispy pastry and creamy potato filling, infused with aromatic spices like curry powder, onions, and garlic. Each bite offers a harmonious blend of tender potatoes wrapped in a golden, flaky crust. Whether as a quick snack or a cozy evening treat, Karipap Kentang guarantees a flavorful experience that's simply irresistible.</p>
                        </div>
                        <div class="product-box">
                            <p style="font-weight: bold; font-size: 23px; text-align: center">Karipap Sayur</p>
                            <img src="Others/Sayur.png" alt="Karipap-Kentang">
                            <p><a>Karipap Sayur, </a>Malaysian vegetable curry puffs, feature a crispy pastry filled with a flavorful mix of assorted veggies. Bursting with colors and spices, each bite offers tender vegetables like potatoes, carrots, and peas, infused with curry powder and onions. Perfect as a light snack or appetizer, Karipap Sayur delivers a satisfying and wholesome culinary experience for vegetarians and veggie lovers.</p>
                        </div>
                        <div class="product-box">
                            <p style="font-weight: bold; font-size: 23px; text-align: center">Karipap Ayam</p>
                            <img src="Others/Ayam.png" alt="Karipap-Kentang">
                            <p><a>Karipap Ayam, </a>Malaysian chicken curry puffs, boast a golden pastry crust filled with flavorful minced chicken and aromatic spices. Each bite offers tender chicken, fragrant curry powder, and hints of garlic and ginger wrapped in a crispy shell. Whether as a quick snack or appetizer, Karipap Ayam ensures a satisfying culinary experience that's bound to leave you wanting more.</p>
                        </div>
                        <div class="product-box">
                            <p style="font-weight: bold; font-size: 23px; text-align: center">Karipap Daging</p>
                            <img src="Others/Daging.png" alt="Karipap-Kentang">
                            <p><a>Karipap Daging </a> is a savory Malaysian pastry filled with seasoned minced meat, usually beef or chicken, wrapped in a crispy golden crust. Bursting with aromatic spices like curry powder, lemongrass, and chili, it offers a delightful fusion of flavors, from tender meat richness to caramelized onion sweetness. Ideal for a quick snack or appetizer, it promises to satisfy cravings with its irresistible texture and taste, perfect for anyone seeking a flavorful culinary adventure.</p>
                        </div>
                        <div class="product-box">
                            <p style="font-weight: bold; font-size: 23px; text-align: center">Karipap Sardin</p>
                            <img src="Others/Sardin.png" alt="Karipap-Kentang">
                            <p><a>Karipap Sardin, </a>or sardine curry puffs, are a delicious Malaysian snack featuring a crispy pastry shell filled with savory sardine filling. Bursting with flavor, each bite combines the richness of sardines with aromatic spices like curry powder, onions, and chili. Whether on-the-go or as an appetizer, Karipap Sardin promises a satisfying and flavorful experience for any occasion.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="about-page section" id="about">
        <div class="container1">
            <div class="content">
                <div class="head-title">
                    <h1>About Us</h1>
                    <div class="about-main">
                        <div class="about-content item-1">
                            <img src="Others/karipap0.jpeg" alt="karipap0">
                            <div class="text-1">
                                <h2>Our Story</h2>
                                <p>Crisp Karipap was founded by a group of food enthusiasts who share a deep love for Malaysian cuisine. Growing up, we cherished the moments spent with family, enjoying freshly made karipap bursting with flavorful fillings. However, as our lives became busier, we realized that finding time to make traditional karipap from scratch was becoming increasingly difficult. Driven by our love for this beloved snack, we embarked on a mission to create a solution that would allow everyone to indulge in the irresistible taste of karipap without the hassle. Thus, Crisp Karipap was born.</p>
                            </div>
                        </div>
                        <div class="about-content item-2">
                            <div class="text-1">
                                <h2>Our Mission</h2>
                                <p>Our mission at Crisp Karipap is simple: to deliver convenience without compromising on quality or taste. We believe that everyone deserves to experience the joy of biting into a perfectly crisp karipap filled with mouthwatering ingredients, whether they're a busy professional, a student far from home, or simply craving a taste of Malaysia.</p>
                            </div>
                            <img src="Others/karipap1blur.jpg" alt="karipap1">        
                        </div>
                        <div class="about-content item-3">
                            <img src="Others/karipap2cut.jpg" alt="karipap2">
                            <div class="text-1">
                                <h2>Our Promise</h2>
                                <p>When you choose Crisp Karipap, you can trust that you're getting more than just a snack – you're getting a taste of tradition, convenience, and quality. We take pride in using only the finest ingredients, preparing each karipap with love and care, and delivering them to your doorstep with speed and efficiency.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="contact-page section" id="contact">
        <div class="container1">
            <div class="content">
                <div class="head-title">
                    <h1>Contact Us</h1>
                    <div class="icon-section">
                        <div class="icon-part">
                            <span class="contact-icon"><i class="far fa-id-card"></i></span>
                                <h2>Contact</h2>
                            <div class="icon-first">
                                <p>Business: <br>
                                +60-168836734</p>
                            </div>
                        </div>
                        <div class="icon-part">
                            <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span> 
                            <h2>Location</h2>
                            <div class="icon-first">
                                <p>Blok B, Apartment Sutera, <br>
                                Jalan Seksyen 3/1a, Taman Kajang Utama, <br>
                                43000 Kajang, Selangor</p>
                            </div>
                        </div>
                        <div class="icon-part">
                            <span class="contact-icon"><i class="fas fa-hashtag"></i></span> 
                            <h2>Social</h2>
                            <div class="icon-first">
                                <div class="icon-part-only">
                                    <i class="fab fa-instagram"></i>
                                    <p>CrispKaripapIG</p>
                                    </div>
                                <div class="icon-part-only">
                                    <i class="fab fa-facebook-f"></i>
                                    <p>CrispKaripapFB</p>
                                    </div>
                                <div class="icon-part-only">
                                    <i class="fab fa-telegram-plane"></i>
                                    <p>CrispKaripapTL</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Contact form -->
                    <style>
                        .contact-form {
                            width: 40%;
                            border-radius: 25px;
                            box-shadow: 0 0 15px black;
                            margin-top: 20px;
                            padding: 20px 20px;
                            background-color: #f2f2f2; /* Light grey background color */
                        }

                        .contact-form h2 {
                            margin-top: 0;
                            margin-bottom: 20px;
                            text-align: center;
                            color: #333; /* Dark grey text color */
                        }

                        .contact-form input[type="text"],
                        .contact-form input[type="email"],
                        .contact-form textarea {
                            width: 100%;
                            padding: 10px;
                            margin: 8px 0;
                            box-sizing: border-box;
                            border: 1px solid #ccc; /* Light grey border */
                            border-radius: 5px;
                        }

                        .contact-form textarea {
                            height: 150px; /* Set the height of the textarea */
                        }

                        .contact-form input[type="submit"] {
                            background-color: #4CAF50; /* Green submit button */
                            color: white; /* White text color */
                            padding: 10px 20px;
                            border: none;
                            border-radius: 5px;
                            cursor: pointer;
                            width: 100%;
                            box-shadow: 0 0 15px black;
                            transition: all 0.3s ease; /* Smooth transition for background color */
                        }

                        .contact-form input[type="submit"]:hover {
                            background-color: #45a049; /* Darker green hover color */
                            transform: scale(1.02);
                        }
                    </style>
                    <?php if($_SESSION['role'] !== "admin"): ?>
                        <div class="contact-form">
                            <h2>Contact Us Form</h2>
                            <form method="post" action="#contact">
                                <label for="name">Name:</label><br>
                                <input type="text" id="name" name="name" required><br><br>
                                <label for="email">Email:</label><br>
                                <input type="email" id="email" name="email" required><br><br>
                                <label for="message">Message:</label><br>
                                <textarea id="message" name="message" rows="4" required></textarea><br><br>
                                <input type="submit" name="submit_contact" value="Submit">
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- BOTTOM-NAV -->
    <div class="bottom-main">
        <div class="bottom-left">
            <img src="Others\Crisp Karipap_transparent.png" alt="CrispKaripap">
            <div class="bottom-left-content">
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#product">Product</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="bottom-right">
            <h3>Social</h3>
            <div class="bottom-icon-first">
                <div class="bottom-icon-part-only">
                    <i class="fab fa-instagram"></i>
                    <p><a href="#">CrispKaripapIG</a></p>
                    </div>
                <div class="bottom-icon-part-only">
                    <i class="fab fa-facebook-f"></i>
                    <p><a href="#">CrispKaripapFB</a></p>
                    </div>
                <div class="bottom-icon-part-only">
                    <i class="fab fa-telegram-plane"></i>
                    <p><a href="#">CrispKaripapTL</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript-->
    <script src="home-script.js"></script>
    <script>
    function toggleCartDropdown() {
        var dropdownContent = document.getElementById("cartDropdownContent");
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }
</script>
    <script>
    function toggleCartDropdown() {
        var dropdownContent = document.getElementById("cartDropdownContent");
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }
    </script>
    <script>
    function checkoutOrder() {
        // Redirect the user to the checkout page
        window.location.href = 'checkout.php';
    }
</script>
</body>
</html>
