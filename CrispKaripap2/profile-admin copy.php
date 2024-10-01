<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: login-admin.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style6.css">
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
            background-repeat: no-repeat;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
</style>
<body>
<div id="main-container">
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">Logo</a> </p>
        </div>

        <div class="right-links">
            <?php 
                $id = $_SESSION['id'];
                $query = mysqli_query($con,"SELECT * FROM admin WHERE Id=$id");

                while($result = mysqli_fetch_assoc($query)){
                    $res_Uname = $result['Username'];
                    $res_Email = $result['Email'];
                    $res_Age = $result['Age'];
                    $res_id = $result['Id'];
                }
            ?>
            <nav class="navigation">
                <a href="main.php#home">Home</a>
                <a href="main.php#product">Product</a>
                <a href="main.php#contact">Contact</a>
                <a href="main.php#about">About</a>
                <div class="dropdown">
                    <button class="dropbtn"><span class="profile-icon"><i class="fas fa-user"></i></span> 
                        <div class="dropdown-content">
                            <a href="profile-admin.php">Profile</a>
                            <a href="php/logout.php">Logout</a>
                        </div>
                    </button>
                </div>
                <b style="font-size: 14px;"><?php echo $res_Uname ?></b>
            </nav>
        </div>
    </div>

    <main>
        <div class="main-box top">
            <h1>Admin</h1>
            <div class="middle">
                <div class="box">
                    <p>Hello Admin <b><?php echo $res_Uname ?></b>, Welcome</p>
                </div>
                <div class="box">
                    <p>Your email is <b><?php echo $res_Email ?></b>.</p>
                </div>
            </div>
            <div class="bottom">
                <div class="box">
                    <p>And you are <b><?php echo $res_Age ?> years old</b>.</p> 
                </div>
            </div>
            <br>
            <h1>Option</h1>
            <div class="option-main">
                <div class="option">
                    <div class="top">
                        <button class="box-last" onclick="toggleUserList()">User List</button>
                    </div>
                    <div class="top">
                        <a href="user-search">
                        <div class="box-last">
                            <p>User Search</p>
                        </div>
                        </a>
                    </div>     
                    <div class="top">
                        <button class="box-last" onclick="toggleAdminList()">Admin List</button>
                    </div>
                    <div class="top">
                        <a href="admin-search">
                        <div class="box-last">
                            <p>Admin Search</p>
                        </div>
                        </a>
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
        </div>

    </main>
    </div>


    

    <!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        <h2>Edit User</h2>
        <form id="editUserForm" method="post">
            <input type="hidden" name="userId" id="editUserId">
            <label for="editUsername">Username:</label>
            <input type="text" id="editUsername" name="editUsername" required>
            <label for="editEmail">Email:</label>
            <input type="email" id="editEmail" name="editEmail" required>
            <label for="editAge">Age:</label>
            <input type="number" id="editAge" name="editAge" required>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

<!-- Edit Admin Modal -->
<div id="editAdminModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editAdminModal')">&times;</span>
        <h2>Edit Admin</h2>
        <form id="editAdminForm" method="post">
            <input type="hidden" name="adminId" id="editAdminId">
            <label for="editAdminUsername">Username:</label>
            <input type="text" id="editAdminUsername" name="editAdminUsername" required>
            <label for="editAdminEmail">Email:</label>
            <input type="email" id="editAdminEmail" name="editAdminEmail" required>
            <label for="editAdminAge">Age:</label>
            <input type="number" id="editAdminAge" name="editAdminAge" required>
            <button type="submit">Save Changes</button>
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
        /* Modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            border-radius: 10px;
        }

        /* Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .notification {
    position: fixed;
    top: 60px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #4CAF50;
    color: white;
    text-align: center;
    padding: 15px;
    border-radius: 5px;
    z-index: 9999;
}
    </style>


<script>
    function toggleUserList() {
    var userListTable = document.getElementById("userListTable");
    var adminListTable = document.getElementById("adminListTable");
    if (userListTable.style.display === "none") {
        userListTable.style.display = "block";
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
    } else {
        adminListTable.style.display = "none";
    }
    userListTable.style.display = "none";
}

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
                    // showSuccessNotification("User " + email + " has been deleted successfully."); // Remove this line
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
                        showSuccessNotification("Admin " + email + " has been deleted successfully.");
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
                    showSuccessNotification(); // Show success notification
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
                if (xhr.status == 200) {
                    if (xhr.responseText === "success") {
                        closeModal('editAdminModal'); // Close the modal
                        updateAdminTable(); // Update admin table
                        showSuccessNotification(); // Show success notification
                    } else {
                        alert("Failed to update admin data.");
                    }
                } else {
                    alert("Error: " + xhr.status); // Alert if there's an HTTP error
                }
            }
        };
        xhr.send(formData); // Send form data
    });

// Function to show success notification
function showSuccessNotification(message) {
    var confirmation = confirm(message);
    if (confirmation) {
        var notification = document.createElement('div');
        // notification.className = 'notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        // Blur main content container
        document.getElementById('main-container').style.filter = 'blur(5px)';

        setTimeout(function () {
            notification.style.opacity = '0'; // Fade out notification
            setTimeout(function () {
                notification.style.display = 'none'; // Hide notification
                document.getElementById('main-container').style.filter = 'none'; // Remove background blur from main content container
            }, 1000); // Delay for fade out animation
        }, 2000); // Hide notification after 2 seconds
    }
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

</body>
</html>
