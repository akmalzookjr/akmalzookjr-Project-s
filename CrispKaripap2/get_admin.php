<?php
include("php/config.php");
$id = $_GET['id'];
$query = mysqli_query($con, "SELECT * FROM admin WHERE Id=$id");
$admin = mysqli_fetch_assoc($query);
echo json_encode($admin);
?>