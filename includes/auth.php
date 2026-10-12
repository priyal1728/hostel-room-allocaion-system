<?php
require_once 'config.php';

// Unified authentication system for students and admins

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'student' || $_SESSION['user_type'] === 'admin');
}

// Check if user is student
function isStudent() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login.php');
    }
}

// Redirect if not student
function requireStudentLogin() {
    if (!isStudent()) {
        if (isAdmin()) {
            redirect('admin/dashboard.php');
        }
        redirect('auth/login.php');
    }
}

// Redirect if not admin
function requireAdminLogin() {
    if (!isAdmin()) {
        if (isStudent()) {
            redirect('student/dashboard.php');
        }
        redirect('admin/login.php');
    }
}

// Logout user
function logout() {
    session_unset();
    session_destroy();
    redirect('index.php');
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
    $stmt = mysqli_prepare($conn, 'SELECT * FROM students WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get student by email
function getStudentByEmail($email) {
    global $conn;
    $stmt = mysqli_prepare($conn, 'SELECT * FROM students WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Register student
function registerStudent($name, $email, $password, $phone, $enrollment_no, $branch, $year, $gender, $dob, $address, $parent_name, $parent_phone) {
    global $conn;

    // Check if email exists
    if (getStudentByEmail($email)) {
        return ['success' => false, 'message' => 'Email already registered!'];
    }

    $hashed_password = hashPassword($password);
    $stmt = mysqli_prepare($conn, 'INSERT INTO students (name, email, password, phone, enrollment_no, branch, year, gender, dob, address, parent_name, parent_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    // types: name(s), email(s), password(s), phone(s), enrollment_no(s), branch(s), year(i), gender(s), dob(s), address(s), parent_name(s), parent_phone(s)
    mysqli_stmt_bind_param($stmt, 'ssssssisssss', $name, $email, $hashed_password, $phone, $enrollment_no, $branch, $year, $gender, $dob, $address, $parent_name, $parent_phone);

    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Registration successful!'];
    }

    return ['success' => false, 'message' => 'Registration failed: ' . mysqli_error($conn)];
}

// Login student
function loginStudent($email, $password) {
    $student = getStudentByEmail($email);

    if (!$student) {
        return ['success' => false, 'message' => 'Email not found!'];
    }

    if (!verifyPassword($password, $student['password'])) {
        return ['success' => false, 'message' => 'Incorrect password!'];
    }

    $_SESSION['student_id'] = $student['id'];
    $_SESSION['student_name'] = $student['name'];
    $_SESSION['student_email'] = $student['email'];
    $_SESSION['user_type'] = 'student';

    return ['success' => true, 'message' => 'Login successful!'];
}

// Login admin
function loginAdmin($username, $password) {
    global $conn;

    $stmt = mysqli_prepare($conn, 'SELECT * FROM admins WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$admin) {
        return ['success' => false, 'message' => 'Invalid admin username or password.'];
    }

    if (!password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Invalid admin username or password.'];
    }

    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['user_type'] = 'admin';

    return ['success' => true, 'message' => 'Admin login successful!'];
}

// Get student room allocation
function getStudentRoomAllocation($student_id) {
    global $conn;
    $query = 'SELECT a.*, r.room_number, r.block, r.floor, r.capacity FROM room_allocations a JOIN rooms r ON a.room_id = r.id WHERE a.student_id = ? AND a.status = "Active" LIMIT 1';
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get all rooms
function getAllRooms() {
    global $conn;
    $query = 'SELECT * FROM rooms ORDER BY block, room_number';
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get admin dashboard stats
function getAdminDashboardStats() {
    global $conn;
    $stats = [
        'total_students' => 0,
        'total_rooms' => 0,
        'available_rooms' => 0,
        'occupied_rooms' => 0,
        'full_rooms' => 0,
        'pending_requests' => 0,
    ];

    $stats['total_students'] = (int)mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM students'))['total'];
    $stats['total_rooms'] = (int)mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM rooms'))['total'];
    $stats['available_rooms'] = (int)mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM rooms WHERE status = "Available"'))['total'];
    $stats['occupied_rooms'] = (int)mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM rooms WHERE current_occupancy > 0'))['total'];
    $stats['full_rooms'] = (int)mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM rooms WHERE current_occupancy >= capacity'))['total'];
    $stats['pending_requests'] = (int)mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM room_requests WHERE status = "Pending"'))['total'];

    return $stats;
}

// Get allocation requests
function getAllocationRequests() {
    global $conn;
    $query = 'SELECT rr.id, rr.student_id, rr.room_id, rr.requested_on, rr.status, s.name AS student_name, s.enrollment_no, r.room_number, r.block FROM room_requests rr JOIN students s ON rr.student_id = s.id JOIN rooms r ON rr.room_id = r.id ORDER BY rr.requested_on DESC';
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Process allocation request
function processAllocationRequest($request_id, $decision) {
    global $conn;

    $stmt = mysqli_prepare($conn, 'SELECT * FROM room_requests WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $request_id);
    mysqli_stmt_execute($stmt);
    $request = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$request) {
        return ['success' => false, 'message' => 'Request not found.'];
    }

    if ($request['status'] !== 'Pending') {
        return ['success' => false, 'message' => 'This request has already been processed.'];
    }

    if ($decision === 'Rejected') {
        $update = mysqli_prepare($conn, 'UPDATE room_requests SET status = ? WHERE id = ?');
        mysqli_stmt_bind_param($update, 'si', $decision, $request_id);
        mysqli_stmt_execute($update);
        return ['success' => true, 'message' => 'Request rejected successfully.'];
    }

    $roomStmt = mysqli_prepare($conn, 'SELECT * FROM rooms WHERE id = ?');
    mysqli_stmt_bind_param($roomStmt, 'i', $request['room_id']);
    mysqli_stmt_execute($roomStmt);
    $room = mysqli_fetch_assoc(mysqli_stmt_get_result($roomStmt));

    if (!$room || $room['current_occupancy'] >= $room['capacity']) {
        return ['success' => false, 'message' => 'Selected room is not available.'];
    }

    $allocStmt = mysqli_prepare($conn, 'INSERT INTO room_allocations (student_id, room_id, allocation_date, status) VALUES (?, ?, CURDATE(), "Active")');
    mysqli_stmt_bind_param($allocStmt, 'ii', $request['student_id'], $request['room_id']);
    if (!mysqli_stmt_execute($allocStmt)) {
        return ['success' => false, 'message' => 'Unable to allocate room.'];
    }

    mysqli_query($conn, 'UPDATE rooms SET current_occupancy = current_occupancy + 1, status = IF(current_occupancy + 1 >= capacity, "Full", "Available") WHERE id = ' . (int)$request['room_id']);
    mysqli_query($conn, 'UPDATE room_requests SET status = "Approved" WHERE id = ' . (int)$request_id);

    return ['success' => true, 'message' => 'Request approved and room allocated.'];
}

// Create admin if not exists
function createAdminIfNeeded() {
    global $conn;
    $check = mysqli_query($conn, 'SELECT * FROM admins LIMIT 1');
    if (mysqli_num_rows($check) === 0) {
        $password = password_hash('admin123', PASSWORD_BCRYPT);
        mysqli_query($conn, "INSERT INTO admins (username, password, name) VALUES ('admin', '$password', 'Administrator')");
    }
}
?>
