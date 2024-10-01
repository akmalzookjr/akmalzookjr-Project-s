<?php
// Include database connection
include("php/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['addUsername'];
    $email = $_POST['addEmail'];
    $age = $_POST['addAge'];
    $password = $_POST['addPassword'];
    $confirmPassword = $_POST['addUserConfirmPassword']; // Changed to match form field name

    // Check if passwords match
    if ($password !== $confirmPassword) {
        // Return error message for mismatched passwords
        echo "error_password_mismatch";
    } else {
        // Verify unique email
        $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");
        
        if(mysqli_num_rows($verify_query) != 0) {
            // Email already exists
            echo "error_email_exists"; // Return specific error message
        } else {
            // Insert user data into the users table
            $insert_query = mysqli_query($con, "INSERT INTO users (Username, Email, Password, Age, Role) VALUES ('$username', '$email', '$password', $age, 'users')");
            
            if($insert_query) {
                // Insertion successful
                echo "success";
            } else {
                // Insertion failed
                echo "error_insert";
            }
        }
    }

    // Close connection
    mysqli_close($con);
}
?>
