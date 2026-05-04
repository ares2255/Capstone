<?php
session_start();
include "config/db.php";

// Check if user is logged in
if(!isset($_SESSION['admin_username']) && !isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Check if an ID was provided in the URL
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete the specific print job from the database
    $query = "DELETE FROM print_jobs WHERE id = '$id'";
    
    if(mysqli_query($conn, $query)) {
        // Success: Redirect back to the printing management page
        header("Location: printing.php?msg=voided");
        exit();
    } else {
        // Error handling
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // If no ID was provided, just go back

header("Location: printing.php?msg=removed");
exit();
}
?>