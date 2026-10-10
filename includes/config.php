<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hostel_db');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
mysqli_select_db($conn, DB_NAME);

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// Define base URL
define('BASE_URL', 'http://localhost/hostel-room-allocation-system/');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
