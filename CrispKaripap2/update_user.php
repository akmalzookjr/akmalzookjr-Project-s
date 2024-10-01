<?php
include("php/config.php"); // Make sure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract POST variables
    $userId = $_POST['editUserId'];
    $username = $_POST['editUsername'];
    $email = $_POST['editEmail'];
    $age = $_POST['editAge'];

    // Update query
    $query = mysqli_query($con, "UPDATE users SET Username='$username', Email='$email', Age='$age' WHERE Id='$userId'");

    if ($query) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
