<?php
session_start();
include "config/db.php";

// CRITICAL FIX: Set this to your local timezone so PHP matches your computer clock
date_default_timezone_set('Asia/Manila'); 

if(isset($_GET['id'])) {
    // 1. Sanitize the PC ID
    $pc_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 2. Handle the minutes/time_limit variable
    if(isset($_GET['mins']) && is_numeric($_GET['mins'])) {
        $mins_raw = abs(intval($_GET['mins'])); 
        $time_limit_val = "'$mins_raw'"; 
    } else {
        $time_limit_val = "NULL"; 
    }

    // This now generates the correct local time
    $start_time = date("Y-m-d H:i:s");

    // 3. Update the PC status to active
    $update_pc = "UPDATE pcs SET status = 'active' WHERE id = '$pc_id'";
    
    if($conn->query($update_pc)) {
        // 4. Create the session record
        $insert_session = "INSERT INTO sessions (pc_id, start_time, time_limit) 
                           VALUES ('$pc_id', '$start_time', $time_limit_val)";
        
        if($conn->query($insert_session)) {
            header("Location: counter.php?status=started");
            exit();
        } else {
            die("Database Error: " . $conn->error);
        }
    } else {
        die("PC Update Error: " . $conn->error);
    }
} else {
    header("Location: counter.php");
}
?>