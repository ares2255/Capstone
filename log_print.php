<?php
include "config/db.php";

$type = $_POST['type'];
$pages = $_POST['pages'];
$price = $_POST['price'];

mysqli_query($conn,"INSERT INTO print_jobs(type,pages,price,created_at)
VALUES('$type','$pages','$price',NOW())");

echo "success";
?>