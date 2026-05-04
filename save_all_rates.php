<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and grab all inputs from your form
    $h1  = mysqli_real_escape_string($conn, $_POST['hour_rate']);
    $h3  = mysqli_real_escape_string($conn, $_POST['rate_3hr']);
    $h5  = mysqli_real_escape_string($conn, $_POST['rate_5hr']);
    $h7  = mysqli_real_escape_string($conn, $_POST['rate_7hr']);
    $h12 = mysqli_real_escape_string($conn, $_POST['rate_12hr']);
    $min = mysqli_real_escape_string($conn, $_POST['min_charge']);
    $bw  = mysqli_real_escape_string($conn, $_POST['bw_rate']);
    $col = mysqli_real_escape_string($conn, $_POST['color_rate']);

    // This updates the single settings row (ID 1)
    $sql = "UPDATE settings SET 
            hourly_rate = '$h1', 
            rate_3hr = '$h3', 
            rate_5hr = '$h5', 
            rate_7hr = '$h7', 
            rate_12hr = '$h12', 
            minimum_charge = '$min', 
            bw_rate = '$bw', 
            color_rate = '$col' 
            WHERE id = 1";

    if (mysqli_query($conn, $sql)) {
        header("Location: settings.php?status=success");
        exit();
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}
?>