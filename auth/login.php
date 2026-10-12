<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$error = '';

// If already logged in, redirect to the correct dashboard
if (isLoggedIn()) {
    if (isStudent()) {
        redirect('student/dashboard.php');
    }
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password!";
    } else {
        $result = loginStudent($email, $password);
        if ($result['success']) {
            header("Location: " . BASE_URL . "student/dashboard.php");
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Hostel Management</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="form-container login-container">
            <div class="form-header">
                <h1>Student Login</h1>
                <p>Access your hostel account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
