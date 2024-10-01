<?php
// Include database connection
include("php/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['addAdminUsername']; // Updated
    $email = $_POST['addAdminEmail']; // Updated
    $age = $_POST['addAdminAge']; // Updated
    $password = $_POST['addAdminPassword']; // Updated
    $confirmPassword = $_POST['addAdminConfirmPassword']; // Added
    $code = $_POST['addAdminCode']; // Updated

    // Correct code
    $correct_code = "1234";

    // Check if the entered code matches the correct one
    if ($code != $correct_code) {
        // Return error message for wrong code
        echo "error_wrong_code";
    } else {
        // Check if passwords match
        if ($password !== $confirmPassword) {
            // Return error message for mismatched passwords
            echo "error_password_mismatch";
        } else {
            // Proceed with registration

            // Verify unique email
            $verify_query = mysqli_query($con, "SELECT Email FROM admin WHERE Email='$email'");

            if (mysqli_num_rows($verify_query) != 0) {
                // Email already exists
                echo "error_email_exists";
            } else {
                // Insert admin data into the admin table
                $insert_query = mysqli_query($con, "INSERT INTO admin(Username,Email,Age,Password,Role) VALUES('$username','$email','$age','$password','admin')");

                if ($insert_query) {
                    // Insertion successful
                    echo "success";
                } else {
                    // Insertion failed
                    echo "error_insert";
                }
            }
        }
    }

    // Close connection
    mysqli_close($con);
}
?>
