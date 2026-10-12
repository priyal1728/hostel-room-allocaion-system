<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdminLogin();

$stats = getAdminDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Hostel Admin</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="rooms.php">Room Management</a></li>
                <li><a href="requests.php">Requests</a></li>
                <li><a href="../auth/logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></p>
        </div>

        <div class="stats-grid">
            <div class="card stat-card">
                <h4><?php echo $stats['total_students']; ?></h4>
                <p>Total Students</p>
            </div>
            <div class="card stat-card">
                <h4><?php echo $stats['total_rooms']; ?></h4>
                <p>Total Rooms</p>
            </div>
            <div class="card stat-card">
                <h4><?php echo $stats['available_rooms']; ?></h4>
                <p>Available Rooms</p>
            </div>
            <div class="card stat-card">
                <h4><?php echo $stats['pending_requests']; ?></h4>
                <p>Pending Requests</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Quick Actions</h3>
                <div class="action-group">
                    <a href="rooms.php" class="btn btn-primary">Manage Rooms</a>
                    <a href="requests.php" class="btn btn-secondary">Review Requests</a>
                </div>
            </div>

            <div class="card">
                <h3>Room Status</h3>
                <p><strong>Occupied:</strong> <?php echo $stats['occupied_rooms']; ?></p>
                <p><strong>Available:</strong> <?php echo $stats['available_rooms']; ?></p>
                <p><strong>Full:</strong> <?php echo $stats['full_rooms']; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
