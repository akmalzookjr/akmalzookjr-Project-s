<?php
// Start PHP session and include configuration file
session_start();
include("php/config.php");

// Redirect to login page if user is not authenticated
if (!isset($_SESSION['valid'])) {
    header("Location: login-admin.php");
    exit;
}

// Initialize queries
$userQuery = $adminQuery = null;

// Handle user search requests
if (isset($_POST['user_search']) && !empty($_POST['user_search'])) {
    $searchKeyword = mysqli_real_escape_string($con, $_POST['user_search']); // Prevent SQL injection
    $userQuery = mysqli_query($con, "SELECT Id, Username, Email, Age FROM users WHERE Username LIKE '%$searchKeyword%'");
} else {
    $userQuery = mysqli_query($con, "SELECT Id, Username, Email, Age FROM users");
}

// Handle admin search requests
if (isset($_POST['admin_search']) && !empty($_POST['admin_search'])) {
    $searchKeyword = mysqli_real_escape_string($con, $_POST['admin_search']); // Prevent SQL injection
    $adminQuery = mysqli_query($con, "SELECT Id, Username, Email, Age FROM admin WHERE Username LIKE '%$searchKeyword%'");
} else {
    $adminQuery = mysqli_query($con, "SELECT Id, Username, Email, Age FROM admin");
}
?>
<?php
$id = $_SESSION['id'];
$query = mysqli_query($con,"SELECT * FROM admin WHERE Id='$id'");
$result = mysqli_fetch_assoc($query);
$role = $result['Role']; 

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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_edit_user'])) {
    $userId = $_POST['editUserId']; // Get the user ID from the form
    $username = $_POST['editUsername'];
    $email = $_POST['editEmail'];
    $age = $_POST['editAge'];

    $edit_query = mysqli_query($con, "UPDATE users SET Username='$username', Email='$email', Age='$age' WHERE Id='$userId'");

    if ($edit_query) {
        echo "<script>alert('Profile Updated');</script>";
        // Optionally, redirect to avoid resubmission issues
        header("Location: profile-admin.php");
        exit();
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_edit_admin'])) {
    $adminId = $_POST['editAdminId'];
    $username = $_POST['editAdminUsername'];
    $email = $_POST['editAdminEmail'];
    $age = $_POST['editAdminAge'];

    $edit_query = mysqli_query($con, "UPDATE admin SET Username='$username', Email='$email', Age='$age' WHERE Id='$adminId'");

    if ($edit_query) {
        echo "<script>alert('Profile Updated');</script>";
        // Optionally, redirect to avoid resubmission issues
        header("Location: profile-admin.php");
        exit();
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style14.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add font awesome for icons -->
    <title>Profile - Admin</title>
</head>
<style>
body {
            background: #e4e9f7;
            background-size: cover;
            background-image: url('Others/HD-Karipap.png');
            background-position:center;
            background-size: 1920px;
            height: 100vh;
            background-repeat: repeat;
        }

    body::-webkit-scrollbar {
        display: none;
    }
  
    </style>
<body>
<div id="main-container">
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

    <main>
        <div class="main-box top">
            <h1>Admin</h1>
            <div class="middle">
                <div class="box">
                    <p>Hello Admin <b><?php echo $result['Username'] ?></b>, Welcome</p>
                </div>
                <div class="box">
                    <p>Your email is <b><?php echo $result['Email'] ?></b>.</p>
                </div>
            </div>
            <div class="bottom">
                <div class="box">
                    <p>And you are <b><?php echo $result['Age'] ?> years old</b>.</p> 
                </div>
            </div>
            <br>
            <h1>Option</h1>
            <div class="option-main" style="margin-bottom: 10px;">
                <div class="option">
                <div class="top">
                    <button class="box-last" onclick="toggleUserList()"><i class="fas fa-users"></i>&nbsp; User List</button>
                </div>
                <div class="top">
                    <button class="box-last" onclick="toggleAdminList()"><i class="fas fa-user-shield"></i>&nbsp; Admin List</button>
                </div>
                <div class="top">
                    <button class="box-last" onclick="toggleUserSearchForm()"><i class="fas fa-search"></i>&nbsp; User Search</button>
                </div>
                <div class="top">
                    <button class="box-last" onclick="toggleAdminSearchForm()"><i class="fas fa-search"></i>&nbsp; Admin Search</button>
                </div>
                <div class="top">
                    <button class="box-last" onclick="openAddUserModal()"><i class="fas fa-user-plus"></i>&nbsp; Add User</button>
                </div>
                <div class="top">
                    <button class="box-last" onclick="openAddAdminModal()"><i class="fas fa-user-plus"></i>&nbsp; Add Admin</button>
                </div>

                    <!-- Add User Modal -->
                        <div id="addUserModal" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal('addUserModal')">&times;</span>
                                <h2>Add User</h2>
                                <form id="addUserForm" method="post" onsubmit="return validateUserForm(event)">
                                    <div class="form-group">
                                        <label for="addUsername">Username:</label>
                                        <input type="text" id="addUsername" name="addUsername" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addEmail">Email:</label>
                                        <input type="email" id="addEmail" name="addEmail" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addPassword">Password:</label>
                                        <input type="password" id="addPassword" name="addPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addUserConfirmPassword">Confirm Password:</label>
                                        <input type="password" id="addUserConfirmPassword" name="addUserConfirmPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addAge">Age:</label>
                                        <input type="number" id="addAge" name="addAge" required>
                                    </div>
                                    <button type="submit" class="btn">Add User</button>
                                </form>
                            </div>
                        </div>

                        <!-- Add Admin Modal -->
                        <div id="addAdminModal" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal('addAdminModal')">&times;</span>
                                <h2>Add Admin</h2>
                                <form id="addAdminForm" method="post" onsubmit="return validateAdminForm(event)">
                                    <div class="form-group">
                                        <label for="addAdminUsername">Username:</label>
                                        <input type="text" id="addAdminUsername" name="addAdminUsername" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addAdminEmail">Email:</label>
                                        <input type="email" id="addAdminEmail" name="addAdminEmail" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addAdminPassword">Password:</label>
                                        <input type="password" id="addAdminPassword" name="addAdminPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addAdminConfirmPassword">Confirm Password:</label>
                                        <input type="password" id="addAdminConfirmPassword" name="addAdminConfirmPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addAdminAge">Age:</label>
                                        <input type="number" id="addAdminAge" name="addAdminAge" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addAdminCode">Secret Code:</label>
                                        <input type="text" id="addAdminCode" name="addAdminCode" required>
                                    </div>
                                    <button type="submit" class="btn">Add Admin</button>
                                </form>
                            </div>
                        </div>
                    
                </div>
                
            </div>
            
            <?php
// Check if the query parameter 'table' is set
if(isset($_GET['table'])) {
    $tableToShow = $_GET['table'];
} else {
    // Default to showing the user list table if no query parameter is provided
    $tableToShow = 'userListTable';
}
?>
<!-- User Search Form -->
<div id="userSearchForm" style="display: <?php echo isset($_POST['user_search']) ? 'none' : 'block'; ?>; background-color: <?php echo isset($_POST['user_search']) ? 'lightblue' : 'none'; ?>; margin-top: 20px; margin-bottom: 10px">
    <form method="post">
        <h2>User Search</h2>
        <div class="search-box">
            <input type="text" name="user_search" placeholder="Enter username to search" class="search-input">
            <button type="submit" class="search-btn">Search User</button>
        </div>
    </form>
</div>

<!-- Admin Search Form -->
<div id="adminSearchForm" style="display: <?php echo isset($_POST['admin_search']) ? 'none' : 'block'; ?>; background-color: <?php echo isset($_POST['admin_search']) ? '#lightgreen' : 'none'; ?>; margin-top: 20px; margin-bottom: 10px">
    <form method="post">
        <h2>Admin Search</h2>
        <div class="search-box">
            <input type="text" name="admin_search" placeholder="Enter username to search" class="search-input">
            <button type="submit" class="search-btn">Search Admin</button>
        </div>
    </form>
</div>

<style>

</style>
<!-- Display User Search Results -->
<div id="userSearchResults" style="display: <?php echo isset($_POST['user_search']) ? 'block' : 'none'; ?>; margin-top: 20px; margin-bottom: 20px">
    <?php if(mysqli_num_rows($userQuery) > 0): ?>
        <h2>User Search Result: <br><a style="font-weight: lighter"><?php echo htmlspecialchars($_POST['user_search']); ?></a></h2>
        <table border="1">
            <!-- Table header -->
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Age</th></tr>
            <!-- Table rows -->
            <?php while($row = mysqli_fetch_assoc($userQuery)): ?>
                <tr>
                    <td><?php echo $row['Id']; ?></td>
                    <td><?php echo $row['Username']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['Age']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

<!-- Display Admin Search Results -->
<div id="adminSearchResults" style="display: <?php echo isset($_POST['admin_search']) ? 'block' : 'none'; ?>; margin-top: 20px; margin-bottom: 20px">
    <?php if(mysqli_num_rows($adminQuery) > 0): ?>
        <h2>Admin Search Result: <br><a style="font-weight: lighter"><?php echo htmlspecialchars($_POST['admin_search']); ?></a></h2>
        <table border="1">
            <!-- Table header -->
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Age</th></tr>
            <!-- Table rows -->
            <?php while($row = mysqli_fetch_assoc($adminQuery)): ?>
                <tr>
                    <td><?php echo $row['Id']; ?></td>
                    <td><?php echo $row['Username']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['Age']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No admins found.</p>
    <?php endif; ?>
</div>
            <!-- User List Table -->
<div id="userListTable" style="display: <?php echo ($tableToShow == 'userListTable') ? 'block' : 'none'; ?>;">
    <h2>User List</h2>
    <?php 
        $userQuery = mysqli_query($con, "SELECT Id, Username, Email, Age FROM users");
        if(mysqli_num_rows($userQuery) > 0) {
            echo '<table border="1">';
            echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Age</th><th>Edit/Delete</th></tr>';
            while($row = mysqli_fetch_assoc($userQuery)) {
                echo '<tr>';
                echo '<td>'.$row['Id'].'</td>';
                echo '<td>'.$row['Username'].'</td>';
                echo '<td>'.$row['Email'].'</td>';
                echo '<td>'.$row['Age'].'</td>';
                echo '<td><i class="fas fa-edit" onclick="editUser('.$row['Id'].')"></i> <i class="fas fa-trash-alt" onclick="deleteUser('.$row['Id'].', \''.$row['Email'].'\')"></i></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No users found.</p>';
        }
    ?>
</div>

<!-- Admin List Table -->
<div id="adminListTable" style="display: <?php echo ($tableToShow == 'adminListTable') ? 'block' : 'none'; ?>;">
    <h2>Admin List</h2>
    <?php 
        $adminQuery = mysqli_query($con, "SELECT Id, Username, Email, Age FROM admin");
        if(mysqli_num_rows($adminQuery) > 0) {
            echo '<table border="1">';
            echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Age</th><th>Edit/Delete</th></tr>';
            while($row = mysqli_fetch_assoc($adminQuery)) {
                echo '<tr>';
                echo '<td>'.$row['Id'].'</td>';
                echo '<td>'.$row['Username'].'</td>';
                echo '<td>'.$row['Email'].'</td>';
                echo '<td>'.$row['Age'].'</td>';
                echo '<td><i class="fas fa-edit" onclick="editAdmin('.$row['Id'].')"></i> <i class="fas fa-trash-alt" onclick="deleteAdmin('.$row['Id'].', \''.$row['Email'].'\')"></i></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No admins found.</p>';
        }
    ?>
    </div>  

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        <h2>Edit User</h2>
        <form id="editUserForm" method="post" onsubmit="return validateUserForm(event)">
            <input type="hidden" id="editUserId" name="editUserId">
            <div class="form-group">
                <label for="editUsername">Username:</label>
                <input type="text" id="editUsername" name="editUsername" required>
            </div>
            <div class="form-group">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="editEmail" required>
            </div>
            <div class="form-group">
                <label for="editAge">Age:</label>
                <input type="number" id="editAge" name="editAge" required>
            </div>
            <button type="submit" name="submit_edit_user" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<!-- User Search Modal -->
<div id="userSearchModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('userSearchModal')">&times;</span>
        <h2>User Search</h2>
        <form method="post" onsubmit="searchUser(event)">
            <input type="text" name="user_search" id="user_search_input" placeholder="Enter username to search">
            <button type="submit">Search User</button>
        </form>
    </div>
</div>

<!-- Admin Search Modal -->
<div id="adminSearchModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('adminSearchModal')">&times;</span>
        <h2>Admin Search</h2>
        <form method="post" onsubmit="searchAdmin(event)">
            <input type="text" name="admin_search" id="admin_search_input" placeholder="Enter username to search">
            <button type="submit">Search Admin</button>
        </form>
    </div>
</div>

<!-- Edit Admin Modal -->
<div id="editAdminModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editAdminModal')">&times;</span>
        <h2>Edit Admin</h2>
        <form id="editAdminForm" method="post" onsubmit="return validateAdminForm(event)">
            <input type="hidden" id="editAdminId" name="editAdminId">
            <div class="form-group">
                <label for="editAdminUsername">Username:</label>
                <input type="text" id="editAdminUsername" name="editAdminUsername" required>
            </div>
            <div class="form-group">
                <label for="editAdminEmail">Email:</label>
                <input type="email" id="editAdminEmail" name="editAdminEmail" required>
            </div>
            <div class="form-group">
                <label for="ediAdmintAge">Age:</label>
                <input type="number" id="editAdminAge" name="editAdminAge" required>
            </div>
            <button type="submit" name="submit_edit_admin" class="btn">Save Changes</button>
        </form>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteConfirmationModal')">&times;</span>
        <h2>Confirmation</h2>
        <p id="deleteConfirmationText"></p>
        <button id="confirmDeleteButton">Yes</button>
        <button onclick="closeModal('deleteConfirmationModal')">Cancel</button>
    </div>
</div>


    <!-- Add the following CSS code to your existing CSS styles -->
    <style>

    </style>

<script>
    function toggleCartDropdown() {
        var dropdownContent = document.getElementById("cartDropdownContent");
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }
</script>
<script>

    // JavaScript functions to toggle visibility of search forms
function toggleUserSearchForm() {
    var userSearchForm = document.getElementById("userSearchForm");
    userSearchForm.style.display = (userSearchForm.style.display === "none") ? "block" : "none";

    var adminSearchForm = document.getElementById("adminSearchForm");
    adminSearchForm.style.display = "none"; // Hide admin search form
}

function toggleAdminSearchForm() {
    var adminSearchForm = document.getElementById("adminSearchForm");
    adminSearchForm.style.display = (adminSearchForm.style.display === "none") ? "block" : "none";

    var userSearchForm = document.getElementById("userSearchForm");
    userSearchForm.style.display = "none"; // Hide user search form
}

// Function to open user search modal
function openUserSearchModal() {
    openModal('userSearchModal');
}

// Function to open admin search modal
function openAdminSearchModal() {
    openModal('adminSearchModal');
}



// Initially hide the search forms
document.addEventListener("DOMContentLoaded", function() {
    var userSearchForm = document.getElementById("userSearchForm");
    var adminSearchForm = document.getElementById("adminSearchForm");
    userSearchForm.style.display = "none";
    adminSearchForm.style.display = "none";
});
    // Function to open add user modal
    function openAddUserModal() {
        openModal('addUserModal');
    }

    // Function to open add admin modal
    function openAddAdminModal() {
        openModal('addAdminModal');
    }

    // Event listener for add user form submission
document.getElementById("addUserForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission
    var formData = new FormData(this); // Get form data
    var xhr = new XMLHttpRequest(); // Create new XMLHttpRequest object
    xhr.open("POST", "add_user.php", true); // Open a POST request to add_user.php
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                if (xhr.responseText === "success") {
                    closeModal('addUserModal'); // Close the modal
                    updateUserTable(); // Update user table
                    showSuccessNotification("add", "user", formData.get('addEmail')); // Show success notification
                } else if (xhr.responseText === "error_email_exists") {
                    alert("Email already exists."); // Display error message for existing email
                } else {
                    alert("Failed to add user.");
                }
            } else {
                alert("Error: " + xhr.status); // Alert if there's an HTTP error
            }
        }
    };
    xhr.send(formData); // Send form data
});

// Event listener for add admin form submission
document.getElementById("addAdminForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission
    var formData = new FormData(this); // Get form data
    var xhr = new XMLHttpRequest(); // Create new XMLHttpRequest object
    xhr.open("POST", "add_admin.php", true); // Open a POST request to add_admin.php
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                if (xhr.responseText === "success") {
                    closeModal('addAdminModal'); // Close the modal
                    updateAdminTable(); // Update admin table
                    showSuccessNotification("add", "admin", formData.get('addAdminEmail')); // Show success notification
                } else if (xhr.responseText === "error_wrong_code") {
                    alert("Wrong verify code, Please enter the correct code."); // Display error message for wrong code
                } else if (xhr.responseText === "error_email_exists") {
                    alert("Email already exists."); // Display error message for existing email
                } else {
                    alert("Failed to add admin."); // Display generic error message for insertion failure
                }
            } else {
                alert("Error: " + xhr.status); // Alert if there's an HTTP error
            }
        }
    };
    xhr.send(formData); // Send form data
});
// Function to validate admin form before submission
    function validateAdminForm(event) {
        var password = document.getElementById("addAdminPassword").value;
        var confirmPassword = document.getElementById("addAdminConfirmPassword").value;

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            event.preventDefault(); // Prevent form submission
            return false;
        }
        return true;
    }

    // Function to validate user form before submission
    function validateUserForm(event) {
        var password = document.getElementById("addPassword").value;
        var confirmPassword = document.getElementById("addUserConfirmPassword").value;

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            event.preventDefault(); // Prevent form submission
            return false;
        }
        return true;
    }
    // JavaScript functions to toggle visibility of list tables
function toggleUserList() {
    var userListTable = document.getElementById("userListTable");
    var adminListTable = document.getElementById("adminListTable");
    if (userListTable.style.display === "none") {
        userListTable.style.display = "block";
        // Scroll to the start of the user list table
        userListTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        userListTable.style.display = "none";
    }
    adminListTable.style.display = "none";
}

function toggleAdminList() {
    var userListTable = document.getElementById("userListTable");
    var adminListTable = document.getElementById("adminListTable");
    if (adminListTable.style.display === "none") {
        adminListTable.style.display = "block";
        userListTable.style.display = "none";
        // Scroll to the start of the admin list table
        adminListTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        adminListTable.style.display = "none";
    }
    userListTable.style.display = "none";
}

// Initially hide the list tables
document.addEventListener("DOMContentLoaded", function() {
    var userListTable = document.getElementById("userListTable");
    var adminListTable = document.getElementById("adminListTable");
    userListTable.style.display = "none";
    adminListTable.style.display = "none";
});

function editUser(userId) {
    openModal('editUserModal');
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_user.php?id=" + userId, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var user = JSON.parse(xhr.responseText);
            document.getElementById("editUserId").value = userId;
            document.getElementById("editUsername").value = user.Username;
            document.getElementById("editEmail").value = user.Email;
            document.getElementById("editAge").value = user.Age;
        }
    };
    xhr.send();
}


    // Function to delete user
function deleteUser(userId, email) {
    var confirmationText = "Are you sure to permanently delete user " + email + "?";
    openDeleteConfirmationModal(confirmationText, function() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "delete_user.php?id=" + userId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = xhr.responseText.trim();
                if (response === "success") {
                    showSuccessNotification("delete", "user", email); // Call with "delete" parameter
                    // Reload user table after deletion
                    updateUserTable();
                } else {
                    alert("Failed to delete user " + email + ".");
                }
            }
        };
        xhr.send();
    });
}
    function editAdmin(adminId) {
        openModal('editAdminModal');
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_admin.php?id=" + adminId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var admin = JSON.parse(xhr.responseText);
                document.getElementById("editAdminId").value = adminId;
                document.getElementById("editAdminUsername").value = admin.Username;
                document.getElementById("editAdminEmail").value = admin.Email;
                document.getElementById("editAdminAge").value = admin.Age;
            }
        };
        xhr.send();
    }

    // Function to delete admin
function deleteAdmin(adminId, email) {
    var confirmationText = "Are you sure to permanently delete admin " + email + "?";
    openDeleteConfirmationModal(confirmationText, function() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "delete_admin.php?id=" + adminId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = xhr.responseText.trim();
                if (response === "success") {
                    showSuccessNotification("delete", "admin", email); // Call with "delete" parameter
                    // Reload admin table after deletion
                    updateAdminTable();
                } else {
                    alert("Failed to delete admin " + email + ".");
                }
            }
        };
        xhr.send();
    });
}
    // JavaScript functions to open and close modal dialogs
    function openModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "block";
    }

    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }

    document.getElementById("editUserForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission
    var formData = new FormData(this); // Get form data
    var xhr = new XMLHttpRequest(); // Create new XMLHttpRequest object
    xhr.open("POST", "update_user.php", true); // Open a POST request to update_user.php
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            console.log('Response:', xhr.responseText); // Log the response text
            console.log('Status:', xhr.status); // Log the status code
            if (xhr.status == 200) {
                if (xhr.responseText === "success") {
                    closeModal('editUserModal'); // Close the modal
                    updateUserTable(); // Update user table
                    showSuccessNotification("update", "user"); // Show success notification
                } else {
                    alert("Failed to update user data.");
                }
            } else {
                alert("Error: " + xhr.status); // Alert if there's an HTTP error
            }
        }
    };
    xhr.send(formData); // Send form data
});


    // Event listener for admin form submission
    document.getElementById("editAdminForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission
    var formData = new FormData(this); // Get form data
    var xhr = new XMLHttpRequest(); // Create new XMLHttpRequest object
    xhr.open("POST", "update_admin.php", true); // Open a POST request to update_admin.php
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            console.log('Response:', xhr.responseText); // Log the response text
            console.log('Status:', xhr.status); // Log the status code
            closeModal('editAdminModal'); // Close the modal
            updateAdminTable(); // Update admin table
            showSuccessNotification("update", "admin"); // Show success notification
        }
    };
    xhr.send(formData); // Send form data
});

// Function to show success notification
function showSuccessNotification(operation, userType, email) {
    var notification = document.createElement('div');
    notification.className = 'notification';
    var userTypeString = userType === 'user' ? 'User' : 'Admin'; // Determine user type string
    if (operation === "add") {
        notification.innerHTML = userTypeString + ' <b>' + email + '</b> has been created successfully.';
    } else if (operation === "update") {
        notification.textContent = 'Changes have been successfully made!';
    } else if (operation === "delete") {
        notification.innerHTML = userTypeString + ' <b>' + email + '</b> has been deleted successfully.';
    }
    document.body.appendChild(notification);

    // Blur main content container
    document.getElementById('main-container').style.filter = 'blur(5px)';

    setTimeout(function () {
        notification.style.opacity = '0'; // Fade out notification
        setTimeout(function () {
            notification.style.display = 'none'; // Hide notification
            document.getElementById('main-container').style.filter = 'none'; // Remove background blur from main content container
            setTimeout(function () {
                location.reload(); // Refresh the page after 2 seconds
            }, 2000); // Wait for 2 seconds before refreshing
        }, 1000); // Delay for fade out animation
    }, 2000); // Hide notification after 2 seconds
}

// Function to update the user table
function updateUserTable() {
    setTimeout(function () {
        window.location.href = 'profile-admin.php?table=userListTable'; // Redirect to profile-admin.php after 2 seconds
    }, 2000); // Wait for 2 seconds before redirecting
}

// Function to update the admin table
function updateAdminTable() {
    setTimeout(function () {
        window.location.href = 'profile-admin.php?table=adminListTable'; // Redirect to profile-admin.php with query parameter after 2 seconds
    }, 2000); // Wait for 2 seconds before redirecting
}
function openDeleteConfirmationModal(confirmationText, confirmCallback) {
        document.getElementById("deleteConfirmationText").innerText = confirmationText;
        openModal('deleteConfirmationModal');
        document.getElementById("confirmDeleteButton").onclick = function() {
            closeModal('deleteConfirmationModal');
            confirmCallback();
        };
    }

    function confirmDelete() {
    var confirmCallback = document.getElementById("confirmDeleteCallback").onclick;
    closeModal('deleteConfirmationModal');
    confirmCallback();
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
</body>
</html>
