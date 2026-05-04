<?php
session_start();
include "config/db.php";

// ADD PC
if (isset($_POST['add_pc'])) {
    $pc_name = $_POST['pc_number'];
    // Using 'name' and 'available' to match your previous working files
    $sql = "INSERT INTO pcs (name, status) VALUES ('$pc_name', 'available')";
    mysqli_query($conn, $sql);
}

// DELETE PC
if (isset($_POST['delete_pc'])) {
    $pc_id = $_POST['pc_id'];
    mysqli_query($conn, "DELETE FROM pcs WHERE id = '$pc_id'");
}

// CLEAR ALL
if (isset($_POST['clear_all'])) {
    mysqli_query($conn, "TRUNCATE TABLE pcs");
}

header("Location: settings.php");
exit();
?>