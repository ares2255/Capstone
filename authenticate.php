<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['admin_user'];
    $pass = $_POST['admin_pass'];

    $stmt = $pdo->prepare("SELECT username, password, role FROM users WHERE username = :u");
    $stmt->execute([':u' => $user]);
    $row = $stmt->fetch();

    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['admin_username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: dashboard.php");
        exit();
    }

    header("Location: admin_login.php?error=1");
    exit();
}
?>
