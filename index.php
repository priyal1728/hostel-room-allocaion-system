<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Room Allocation System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Hostel Room Allocation System</h1>
        <p>Student Hostel Management Portal</p>
    </header>

    <main>
        <h2>Welcome to Hostel Management</h2>
        <p>Manage hostel rooms, student registrations, room requests, and admin approvals in one portal.</p>

        <div class="hero-actions">
            <a href="auth/login.php" class="btn btn-primary">Student Login</a>
            <a href="admin/login.php" class="btn btn-secondary">Admin Login</a>
            <a href="auth/register.php" class="btn btn-secondary">Student Registration</a>
        </div>

        <div class="features">
            <div class="feature-box">
                <h3>Student Access</h3>
                <p>Register, login, view profile, and request a room.</p>
            </div>
            <div class="feature-box">
                <h3>Admin Access</h3>
                <p>Review requests, manage rooms, and approve allocations.</p>
            </div>
            <div class="feature-box">
                <h3>Security</h3>
                <p>Session-based authentication and password hashing included.</p>
            </div>
            <div class="feature-box">
                <h3>Database</h3>
                <p>MySQL database with tables for students, rooms, and requests.</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Hostel Management System. All rights reserved.</p>
    </footer>
</body>
</html>