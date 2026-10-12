<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireStudentLogin();

$student = getStudentById($_SESSION['student_id'] ?? 0);
if (!$student) {
    session_unset();
    session_destroy();
    redirect('auth/login.php');
}

$room_allocation = getStudentRoomAllocation($_SESSION['student_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hostel Management</title>
    <link rel="stylesheet" href="../assets/css/student.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Hostel Management</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="room-request.php">Room Request</a></li>
                <li><a href="../auth/logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($student['name'] ?? 'Student'); ?>!</h1>
            <p>Your hostel portal dashboard</p>
        </div>

        <div class="dashboard-grid">
            <!-- Room Allocation Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Room Allocation</h3>
                </div>
                <div class="card-body">
                    <?php if ($room_allocation): ?>
                        <div class="allocation-info">
                            <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room_allocation['room_number'] ?? ''); ?></p>
                            <p><strong>Block:</strong> <?php echo htmlspecialchars($room_allocation['block'] ?? ''); ?></p>
                            <p><strong>Floor:</strong> <?php echo htmlspecialchars($room_allocation['floor'] ?? ''); ?></p>
                            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room_allocation['capacity'] ?? ''); ?> students</p>
                            <p><strong>Allocated Date:</strong> <?php echo date('d-m-Y', strtotime($room_allocation['allocation_date'] ?? 'now')); ?></p>
                        </div>
                    <?php else: ?>
                        <p class="no-allocation">No room allocated yet.</p>
                        <a href="room-request.php" class="btn-secondary">Request a Room</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Student Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Student Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>Enrollment No:</strong> <?php echo htmlspecialchars($student['enrollment_no'] ?? ''); ?></p>
                    <p><strong>Branch:</strong> <?php echo htmlspecialchars($student['branch'] ?? ''); ?></p>
                    <p><strong>Year:</strong> Year <?php echo htmlspecialchars($student['year'] ?? ''); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email'] ?? ''); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone'] ?? ''); ?></p>
                    <a href="profile.php" class="btn-secondary">View Full Profile</a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-section">
            <h2>Quick Stats</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h4><?php echo $room_allocation ? 'Active' : 'Pending'; ?></h4>
                    <p>Room Status</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></h4>
                    <p>Current Year</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo htmlspecialchars($student['branch'] ?? 'N/A'); ?></h4>
                    <p>Branch</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
