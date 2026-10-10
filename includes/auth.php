<?php
require_once 'config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['student_id']);
}

// Check if user is student
function isStudent() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}

// Logout user
function logout() {
    session_destroy();
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Get student by ID
function getStudentById($id) {
    global $conn;
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get student by email
function getStudentByEmail($email) {
    global $conn;
    $query = "SELECT * FROM students WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Register student
function registerStudent($name, $email, $password, $phone, $enrollment_no, $branch, $year, $gender, $dob, $address, $parent_name, $parent_phone) {
    global $conn;
    
    // Check if email exists
    if (getStudentByEmail($email)) {
        return array('success' => false, 'message' => 'Email already registered!');
    }
    
    $hashed_password = hashPassword($password);
    
    $query = "INSERT INTO students (name, email, password, phone, enrollment_no, branch, year, gender, dob, address, parent_name, parent_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssssiisis", $name, $email, $hashed_password, $phone, $enrollment_no, $branch, $year, $gender, $dob, $address, $parent_name, $parent_phone);
    
    if (mysqli_stmt_execute($stmt)) {
        return array('success' => true, 'message' => 'Registration successful!');
    } else {
        return array('success' => false, 'message' => 'Registration failed: ' . mysqli_error($conn));
    }
}

// Login student
function loginStudent($email, $password) {
    $student = getStudentByEmail($email);
    
    if (!$student) {
        return array('success' => false, 'message' => 'Email not found!');
    }
    
    if (!verifyPassword($password, $student['password'])) {
        return array('success' => false, 'message' => 'Incorrect password!');
    }
    
    $_SESSION['student_id'] = $student['id'];
    $_SESSION['student_name'] = $student['name'];
    $_SESSION['student_email'] = $student['email'];
    $_SESSION['user_type'] = 'student';
    
    return array('success' => true, 'message' => 'Login successful!');
}

// Get student room allocation
function getStudentRoomAllocation($student_id) {
    global $conn;
    $query = "SELECT a.*, r.room_number, r.block, r.floor, r.capacity FROM room_allocations a
              JOIN rooms r ON a.room_id = r.id
              WHERE a.student_id = ? AND a.status = 'Active'
              LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>
