<?php
include("php/config.php");

if(isset($_GET['id'])) {
    $userId = $_GET['id'];
    
    // Perform deletion query
    $query = "DELETE FROM users WHERE Id=$userId";
    $result = mysqli_query($con, $query);

    if($result) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>