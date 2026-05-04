<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['pc_id'])) {
    $pc_id = mysqli_real_escape_string($conn, $_POST['pc_id']);

    $sql = "DELETE FROM pcs WHERE id = '$pc_id'";
    
    if ($conn->query($sql)) {
        header("Location: settings.php?status=pc_deleted");
        exit();
    }
}
header("Location: settings.php");
?>