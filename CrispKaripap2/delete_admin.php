<?php
include("php/config.php");

if(isset($_GET['id'])) {
    $adminId = $_GET['id'];
    
    // Perform deletion query
    $query = "DELETE FROM admin WHERE Id=$adminId";
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