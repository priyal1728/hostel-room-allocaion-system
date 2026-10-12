<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    }
    if (isStudent()) {
        redirect('student/dashboard.php');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $result = loginAdmin($username, $password);
        if ($result['success']) {
            redirect('admin/dashboard.php');
        }
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hostel Management</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="form-container login-container">
            <div class="form-header">
                <h1>Admin Login</h1>
                <p>Manage rooms and student requests</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">Login as Admin</button>
            </form>

            <div class="form-footer">
                <p><a href="../index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
