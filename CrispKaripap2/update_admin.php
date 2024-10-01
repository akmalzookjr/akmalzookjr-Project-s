<?php
include("php/config.php"); // Make sure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract POST variables
    $adminId = $_POST['editAdminId'];
    $username = $_POST['editAdminUsername'];
    $email = $_POST['editAdminEmail'];
    $age = $_POST['editAdminAge'];

    // Update query
    $query = mysqli_query($con, "UPDATE admin SET Username='$username', Email='$email', Age='$age' WHERE Id='$adminId'");

    if ($query) {
        echo "success";
    } else {
        echo "error";
    }
}
?>