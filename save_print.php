<?php
session_start();
include_once "config/db.php";

// Redirect if not logged in
if(!isset($_SESSION['username']) && !isset($_SESSION['admin_username'])){
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture and Sanitize Input
    // We use strtoupper to ensure it matches 'BW' or 'COLOR' regardless of input
    $type = strtoupper(mysqli_real_escape_string($conn, $_POST['print_type'])); 
    $pages = intval($_POST['pages']);
    
    if ($pages <= 0) {
        header("Location: printing.php?status=error&msg=Invalid+page+count");
        exit();
    }

    // 2. Fetch Rates from your 'settings' table
    $rates_query = $conn->query("SELECT bw_rate, color_rate FROM settings LIMIT 1");
    $rates = $rates_query->fetch_assoc();
    
    if (!$rates) {
        header("Location: printing.php?status=error&msg=Rates+not+configured");
        exit();
    }

    // 3. Calculate Cost (Matches the 'BW' and 'Color' strings from your UI)
    $unit_price = ($type === 'BW') ? $rates['bw_rate'] : $rates['color_rate'];
    $total_price = $pages * $unit_price;

    // 4. Insert into 'print_jobs' table
    // Note: Ensure your 'type' column in DB can hold 'BW' or 'Color'
    $stmt = $conn->prepare("INSERT INTO print_jobs (type, pages, price, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sid", $type, $pages, $total_price);

    if ($stmt->execute()) {
        header("Location: printing.php?status=success");
    } else {
        header("Location: printing.php?status=db_error");
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: printing.php");
    exit();
}
?>