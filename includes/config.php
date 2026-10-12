<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hostel_db');
define('BASE_URL', 'http://localhost/hostel-room-allocation-system/');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

$sql = 'CREATE DATABASE IF NOT EXISTS ' . DB_NAME;
if (!mysqli_query($conn, $sql)) {
    die('Error creating database: ' . mysqli_error($conn));
}

mysqli_select_db($conn, DB_NAME);
mysqli_set_charset($conn, 'utf8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirect($path) {
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit();
}

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}
?>
