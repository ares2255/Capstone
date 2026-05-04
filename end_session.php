<?php
session_start();
include "config/db.php";
date_default_timezone_set('Asia/Manila');

if(isset($_GET['id'])) {
    $pc_id = mysqli_real_escape_string($conn, $_GET['id']);
    $end_time = date("Y-m-d H:i:s");

    // 1. Fetch the CURRENT rates from the database settings table
    $settings_query = mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
    $rates = mysqli_fetch_assoc($settings_query);

    // 2. Get the session details
    $session_query = "SELECT id, start_time, time_limit FROM sessions 
                      WHERE pc_id = '$pc_id' AND end_time IS NULL 
                      ORDER BY id DESC LIMIT 1";
    $result = $conn->query($session_query);

    if($row = $result->fetch_assoc()) {
        $session_id = $row['id'];
        $start_time = $row['start_time'];
        $time_limit = $row['time_limit'];

        // Calculate elapsed time for Open Time logic
        $start_dt = new DateTime($start_time);
        $end_dt = new DateTime($end_time);
        $interval = $start_dt->diff($end_dt);
        $total_minutes = ($interval->h * 60) + $interval->i;

        // 3. Determine the Price based on the DYNAMIC rates from DB
        $cost = 0;
        if ($time_limit == 60) {
            $cost = $rates['hourly_rate'];  // Uses your setting (e.g. 20.00)
        } elseif ($time_limit == 180) {
            $cost = $rates['rate_3hr'];     // Uses your setting
        } elseif ($time_limit == 300) {
            $cost = $rates['rate_5hr'];     // Uses your setting
        } elseif ($time_limit == 420) {
            $cost = $rates['rate_7hr'];     // Uses your setting
        } elseif ($time_limit == 720) {
            $cost = $rates['rate_12hr'];    // Uses your setting
        } else {
            // Open Time calculation: uses hourly_rate and minimum_charge from DB
            $hourly = $rates['hourly_rate'];
            $min = $rates['minimum_charge'];
            $cost = max($min, ($total_minutes / 60) * $hourly);
        }

        // 4. Record this into the Dashboard (transactions table)
        // IMPORTANT: If you haven't created the 'transactions' table yet, run the SQL query from the previous message
        $pc_name_query = mysqli_query($conn, "SELECT name FROM pcs WHERE id = '$pc_id'");
        $pc_row = mysqli_fetch_assoc($pc_name_query);
        $pc_name = $pc_row['name'];

        $insert_transaction = "INSERT INTO transactions (type, description, amount, time) 
                               VALUES ('Session', '$pc_name', '$cost', '$end_time')";
        $conn->query($insert_transaction);

        // 5. Update the session and PC status
        $update_session = "UPDATE sessions SET end_time = '$end_time', cost = '$cost' WHERE id = '$session_id'";
        $update_pc = "UPDATE pcs SET status = 'available' WHERE id = '$pc_id'";

        if($conn->query($update_session) && $conn->query($update_pc)) {
            header("Location: counter.php?status=ended&paid=$cost&pc=$pc_name");
            exit();
        }
    }
}
header("Location: counter.php");
?>