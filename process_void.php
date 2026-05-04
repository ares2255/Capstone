<?php
session_start();
include "config/db.php";

if(isset($_GET['id']) && isset($_GET['type'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $type = $_GET['type'];

    if($type == 'Session') {
        mysqli_query($conn, "DELETE FROM sessions WHERE id = '$id'");
    } else {
        mysqli_query($conn, "DELETE FROM print_jobs WHERE id = '$id'");
    }
}
header("Location: dashboard.php");
?>