<?php
session_start();
include_once "config/db.php";

if(isset($_SESSION['admin_username']) && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM print_jobs WHERE id = $id");
    header("Location: printing.php?msg=voided");
} else {
    header("Location: printing.php?error=unauthorized");
}
?>