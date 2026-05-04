<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['admin_user'];
    $pass = $_POST['admin_pass'];

    $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify the hashed password
        if (password_verify($pass, $row['password'])) {
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: dashboard.php");
            exit();
        }
    }
    
    // If we reach here, login failed
    header("Location: admin_login.php?error=1");
    exit();
}
?>