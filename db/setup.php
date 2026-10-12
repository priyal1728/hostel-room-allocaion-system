<?php
require_once '../includes/config.php';

function addColumnIfMissing($table, $column, $definition) {
    global $conn;
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($result) === 0) {
        mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN $column $definition");
    }
}

$sql = [
    "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        enrollment_no VARCHAR(50) NOT NULL UNIQUE,
        branch VARCHAR(50) DEFAULT NULL,
        year INT DEFAULT NULL,
        gender VARCHAR(20) DEFAULT NULL,
        dob DATE DEFAULT NULL,
        address TEXT DEFAULT NULL,
        parent_name VARCHAR(100) DEFAULT NULL,
        parent_phone VARCHAR(20) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_number VARCHAR(20) NOT NULL,
        block VARCHAR(30) NOT NULL,
        floor VARCHAR(20) NOT NULL,
        capacity INT NOT NULL,
        current_occupancy INT NOT NULL DEFAULT 0,
        status ENUM('Available','Full') NOT NULL DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS room_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        room_id INT NOT NULL,
        requested_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS room_allocations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        room_id INT NOT NULL,
        allocation_date DATE NOT NULL,
        status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
    )"
];

foreach ($sql as $query) {
    if (!mysqli_query($conn, $query)) {
        die('Database setup failed: ' . mysqli_error($conn));
    }
}

addColumnIfMissing('students', 'password', 'VARCHAR(255) NOT NULL AFTER email');
addColumnIfMissing('students', 'phone', 'VARCHAR(20) NOT NULL AFTER password');
addColumnIfMissing('students', 'enrollment_no', 'VARCHAR(50) NOT NULL UNIQUE AFTER phone');
addColumnIfMissing('students', 'branch', 'VARCHAR(50) DEFAULT NULL AFTER enrollment_no');
addColumnIfMissing('students', 'year', 'INT DEFAULT NULL AFTER branch');
addColumnIfMissing('students', 'gender', 'VARCHAR(20) DEFAULT NULL AFTER year');
addColumnIfMissing('students', 'dob', 'DATE DEFAULT NULL AFTER gender');
addColumnIfMissing('students', 'address', 'TEXT DEFAULT NULL AFTER dob');
addColumnIfMissing('students', 'parent_name', 'VARCHAR(100) DEFAULT NULL AFTER address');
addColumnIfMissing('students', 'parent_phone', 'VARCHAR(20) DEFAULT NULL AFTER parent_name');

createAdminIfNeeded();

$rooms = [
    ['A-101', 'A', '1', 4],
    ['A-102', 'A', '1', 4],
    ['B-201', 'B', '2', 3],
    ['B-202', 'B', '2', 3],
    ['C-301', 'C', '3', 2],
    ['C-302', 'C', '3', 2]
];

foreach ($rooms as $room) {
    $room_number = $room[0];
    $block = $room[1];
    $floor = $room[2];
    $capacity = $room[3];

    $check = mysqli_query($conn, "SELECT id FROM rooms WHERE room_number = '$room_number' AND block = '$block' LIMIT 1");
    if (mysqli_num_rows($check) === 0) {
        mysqli_query($conn, "INSERT INTO rooms (room_number, block, floor, capacity, current_occupancy, status) VALUES ('$room_number', '$block', '$floor', $capacity, 0, 'Available')");
    }
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Database Setup</title><style>body{font-family:Arial,sans-serif;padding:30px;background:#f3f6fb;} .box{max-width:600px;margin:0 auto;background:white;padding:30px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.06);} h2{color:#0f172a;} p{color:#374151;}</style></head><body><div class="box"><h2>Database setup completed</h2><p>The hostel database and required tables were created successfully.</p><p><a href="../index.php">Go to Home Page</a></p></div></body></html>';
