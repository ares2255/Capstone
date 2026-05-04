<?php
// Railway provides MySQL credentials as environment variables.
// Falls back to localhost defaults for local development.

$host   = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
$user   = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
$pass   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$dbname = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'netcafepos';
$port   = (int)(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
