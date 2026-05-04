<?php
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['reg_user'];
    $pass = $_POST['reg_pass'];
    
    // Modern Hashing
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $role = 'admin';

    // 1. Check if user already exists using Prepared Statements
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: register.php?error=exists");
        exit();
    } else {
        // 2. Insert new user
        $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $user, $hashed_pass, $role);
        
        if ($insert->execute()) {
            header("Location: admin_login.php?status=registered");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>