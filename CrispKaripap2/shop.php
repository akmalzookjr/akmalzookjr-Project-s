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
?>
<?php
// Check if the user is logged in and not an admin
if(isset($_SESSION['valid']) && $_SESSION['valid'] && $_SESSION['role'] !== "admin") {
    $showAddToCart = true;
} else {
    $showAddToCart = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style13s.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->
    
    <title>Shop</title>
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
                            <a href="php/logout.php">Logout</a>
                        </div>
                    </button>
                </div>
                <b style="font-size: 14px;"><?php echo $result['Username']; ?></b>
            </nav>
        </div>
    </div>
    <div class="shop-top section" id="top">  
        <div class="shop-top-content">
            <h1>Shop</h1>
        </div>
    </div>
    <div class="shop-middle section" id="top">
        <div class="head-title">
            <div class="shop-top-content">
                <h1>Products <?php if ($role === "admin") { ?><span class="admin-add-product-icon" onclick="showAddProductModal()"><i class="fas fa-plus"></i></span><?php } ?></h1></h1>
                <div class="products-container">
                    <?php
                    // Fetch products from the database
                    $query = "SELECT * FROM Products";
                    $result = mysqli_query($con, $query);

                    // Check if there are products available
                    if (mysqli_num_rows($result) > 0) {
                        // Loop through each product
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <div class="product-image img">
                                <h3><?php echo $row['Name']; ?></h3>
                                <!-- Display the image -->
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['Image']); ?>" alt="<?php echo $row['Name']; ?>">
                                <p>Description: <?php echo $row['Description']; ?></p>
                                
                                <p>Price: <?php echo $row['Price']; ?></p>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="productId" value="<?php echo $row['ProductId']; ?>">
                                    <input style="margin-top:10px" type="number" name="quantity" value="1" min="1">
                                    <!-- Only display the "Add to Cart" button if $showAddToCart is true -->
                                    <?php if ($showAddToCart) { ?>
                                        <br><button type="submit" class="addtocart">Add to Cart</button>
                                    <?php } ?>
                                </form>
                                <!-- Trash icon for deleting the product -->
                                <?php if ($role === "admin") { ?>
                                    <div class="action-icons">
                                        <button class="delete-btn" onclick="deleteProduct(<?php echo $row['ProductId']; ?>)"><i class="fas fa-trash"></i></button>
                                        <button class="edit-btn" onclick="showEditProductModal(<?php echo $row['ProductId']; ?>, '<?php echo $row['Name']; ?>', '<?php echo $row['Description']; ?>', '<?php echo $row['Price']; ?>', '<?php echo base64_encode($row['Image']); ?>')"><i class="fas fa-edit"></i></button>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No products available.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
    <div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="hideEditProductModal()">&times;</span>
        <h2>Edit Product</h2>
        <!-- Inside the editProductModal -->
<form id="editProductForm" onsubmit="submitEditedProduct(); return false;">
    <input type="hidden" id="editProductId" name="editProductId">
    <div class="form-group">
        <label for="editProductImage">New Image:</label>
        <input type="file" id="editProductImage" name="editProductImage" accept="image/*">
    </div>
    <div class="form-group">
        <label for="editProductName">Product Name:</label>
        <input type="text" id="editProductName" name="editProductName" required>
    </div>
    <div class="form-group">
        <label for="editProductDescription">Description:</label>
        <textarea id="editProductDescription" name="editProductDescription" required></textarea>
    </div>
    <div class="form-group">
        <label for="editProductPrice">Price:</label>
        <input type="number" id="editProductPrice" name="editProductPrice" min="0" step="0.01" required>
    </div>
    <div class="form-group">
        <button type="submit" class="btn submit">Save</button>
        <button type="button" class="btn" onclick="hideEditProductModal()">Cancel</button>
    </div>
</form>

    </div>
</div>
            <!-- Pop-out form for admin to add a new product -->

            <div id="addProductModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="hideAddProductModal()">&times;</span>
                    <h2>Add New Product</h2>
                    <form onsubmit="addProduct(); return false;" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="productName">Product Name:</label>
                            <input type="text" id="productName" name="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Description:</label>
                            <textarea id="productDescription" name="productDescription" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="productPrice">Price:</label>
                            <input type="number" id="productPrice" name="productPrice" min="0" step="0.01" required>
                        </div>
                        <!-- Add input field for image upload -->
                        <div class="form-group">
                            <label for="productImage">Image:</label>
                            <input type="file" id="productImage" name="image" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn submit">Add</button>
                            <button type="button" class="btn" onclick="hideAddProductModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
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
<script>
    function deleteCartItem(button) {
    // Get the product id from the data-product-id attribute
    var productId = button.getAttribute('data-product-id');

    // AJAX request to delete the cart item
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_cart_item.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Reload the page to reflect the changes
            window.location.reload();
        }
    };
    xhr.send("productId=" + productId);
}
</script>
<script>
    function addProduct() {
        event.preventDefault(); // Prevent default form submission behavior

        var productName = document.getElementById("productName").value;
        var productDescription = document.getElementById("productDescription").value;
        var productPrice = document.getElementById("productPrice").value;
        var productImage = document.getElementById("productImage").files[0]; // Retrieve the selected image file

        var formData = new FormData(); // Create FormData object to send form data
        formData.append('productName', productName);
        formData.append('productDescription', productDescription);
        formData.append('productPrice', productPrice);
        formData.append('image', productImage); // Append the image file to FormData

        // AJAX request to add_product.php
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "add_product.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    handleAddProductResponse(response);
                } else {
                    // Handle HTTP errors if needed
                    console.error("Error: " + xhr.status);
                }
            }
        };
        xhr.send(formData); // Send FormData object containing form data
    }

    // Function to handle the response from add_product.php
    function handleAddProductResponse(response) {
        if (response.success) {
            alert(response.message); // Display success message
            window.location.reload(); // Refresh the page
        } else {
            alert(response.message); // Display error message
        }
    }
</script>
<script>
    function showAddProductForm() {
        document.getElementById("addProductForm").style.display = "block";
    }

    function hideAddProductForm() {
        document.getElementById("addProductForm").style.display = "none";
    }
</script>
<script>
    function showAddProductModal() {
        console.log("Showing modal...");
        var modal = document.getElementById("addProductModal");
        modal.style.display = "block";
    }

    // Function to hide the modal
    function hideAddProductModal() {
        var modal = document.getElementById("addProductModal");
        modal.style.display = "none";
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        var modal = document.getElementById("addProductModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>
<script>
    function deleteProduct(productId) {
        // Prompt the admin for confirmation
        var confirmation = confirm("Are you sure to delete this product?");
        
        // Log the productId to ensure it's being passed correctly
        console.log("Product ID:", productId);
        
        // If admin confirms deletion
        if (confirmation) {
            // AJAX request to delete the product
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_product.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Reload the page to reflect the changes
                    window.location.reload();
                }
            };
            xhr.send("productId=" + productId);
        }
    }

    function submitEditedProduct() {
    // Retrieve form data
    var productId = document.getElementById("editProductId").value;
    var productName = document.getElementById("editProductName").value;
    var productDescription = document.getElementById("editProductDescription").value;
    var productPrice = document.getElementById("editProductPrice").value;
    var productImage = document.getElementById("editProductImage").files[0]; // Retrieve the selected image file

    var formData = new FormData(); // Create FormData object to send form data
    formData.append('editProductId', productId);
    formData.append('editProductName', productName);
    formData.append('editProductDescription', productDescription);
    formData.append('editProductPrice', productPrice);
    formData.append('editProductImage', productImage); // Append the image file to FormData

    // AJAX request to update_product.php
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_product.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle the response
            handleUpdateProductResponse(xhr.responseText);
        }
    };
    xhr.send(formData);

    // Display pop-up message after successful submission
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Product updated successfully');
            // Reload the page to reflect the changes
            window.location.reload();
        }
    };
}

function showEditProductModal(productId, productName, productDescription, productPrice) {
    var modal = document.getElementById("editProductModal");
    modal.style.display = "block";

    // Pre-fill the form fields with existing product details
    document.getElementById("editProductId").value = productId;
    document.getElementById("editProductName").value = productName;
    document.getElementById("editProductDescription").value = productDescription;
    document.getElementById("editProductPrice").value = productPrice;
}


    function hideEditProductModal() {
        var modal = document.getElementById("editProductModal");
        modal.style.display = "none";
    }
</script>


</body>
</html>
