<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$student = getStudentById($_SESSION['student_id']);
$error = '';
$success = '';
$is_editing = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_phone = trim($_POST['parent_phone'] ?? '');
    
    if (empty($name) || empty($phone)) {
        $error = "Name and phone are required!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be 10 digits!";
    } else {
        $query = "UPDATE students SET name=?, phone=?, address=?, parent_name=?, parent_phone=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $phone, $address, $parent_name, $parent_phone, $_SESSION['student_id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Profile updated successfully!";
            $student = getStudentById($_SESSION['student_id']);
            $_SESSION['student_name'] = $student['name'];
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!verifyPassword($old_password, $student['password'])) {
        $error = "Old password is incorrect!";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } else {
        $hashed_password = hashPassword($new_password);
        $query = "UPDATE students SET password=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $_SESSION['student_id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Password changed successfully!";
        } else {
            $error = "Error changing password: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hostel Management</title>
    <link rel="stylesheet" href="../assets/css/student.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Hostel Management</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php" class="active">My Profile</a></li>
                <li><a href="room-request.php">Room Request</a></li>
                <li><a href="../auth/logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <!-- Personal Information Tab -->
            <div class="profile-section">
                <h2>Personal Information</h2>
                <form method="POST" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Enrollment Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($student['enrollment_no']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Branch</label>
                            <input type="text" value="<?php echo htmlspecialchars($student['branch']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="text" value="Year <?php echo htmlspecialchars($student['year']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" value="<?php echo htmlspecialchars($student['gender']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="text" value="<?php echo date('d-m-Y', strtotime($student['dob'])); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="parent_name">Parent/Guardian Name</label>
                            <input type="text" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($student['parent_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="parent_phone">Parent/Guardian Phone</label>
                            <input type="tel" id="parent_phone" name="parent_phone" value="<?php echo htmlspecialchars($student['parent_phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="btn-primary">Update Profile</button>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="profile-section">
                <h2>Change Password</h2>
                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="old_password">Current Password *</label>
                        <input type="password" id="old_password" name="old_password" placeholder="Enter your current password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Minimum 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password" required>
                    </div>

                    <button type="submit" name="change_password" class="btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
