<?php
include("php/config.php");
$id = $_GET['id'];
$query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");
$user = mysqli_fetch_assoc($query);
echo json_encode($user);
?>