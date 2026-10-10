<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$error = '';
$success = '';

// Check if user already logged in
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "student/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $enrollment_no = trim($_POST['enrollment_no'] ?? '');
    $branch = trim($_POST['branch'] ?? '');
    $year = $_POST['year'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_phone = trim($_POST['parent_phone'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($enrollment_no)) {
        $error = "Please fill all required fields!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be 10 digits!";
    } else {
        $result = registerStudent($name, $email, $password, $phone, $enrollment_no, $branch, $year, $gender, $dob, $address, $parent_name, $parent_phone);
        if ($result['success']) {
            $success = "Registration successful! <a href='login.php'>Click here to login</a>";
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
    <title>Student Registration - Hostel Management</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>Student Registration</h1>
                <p>Create your account to access the hostel system</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="enrollment_no">Enrollment Number *</label>
                        <input type="text" id="enrollment_no" name="enrollment_no" placeholder="e.g., 2023001" 
                               value="<?php echo htmlspecialchars($_POST['enrollment_no'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" placeholder="10 digit number" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="branch">Branch</label>
                        <select id="branch" name="branch">
                            <option value="">Select Branch</option>
                            <option value="CS" <?php echo ($_POST['branch'] ?? '') === 'CS' ? 'selected' : ''; ?>>Computer Science</option>
                            <option value="IT" <?php echo ($_POST['branch'] ?? '') === 'IT' ? 'selected' : ''; ?>>Information Technology</option>
                            <option value="EC" <?php echo ($_POST['branch'] ?? '') === 'EC' ? 'selected' : ''; ?>>Electronics</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select id="year" name="year">
                            <option value="">Select Year</option>
                            <option value="1" <?php echo ($_POST['year'] ?? '') === '1' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo ($_POST['year'] ?? '') === '2' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo ($_POST['year'] ?? '') === '3' ? 'selected' : ''; ?>>3rd Year</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo ($_POST['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($_POST['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($_POST['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" 
                               value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" placeholder="Enter your permanent address" 
                              rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_name">Parent Name</label>
                        <input type="text" id="parent_name" name="parent_name" placeholder="Parent/Guardian name" 
                               value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="parent_phone">Parent Phone</label>
                        <input type="tel" id="parent_phone" name="parent_phone" placeholder="10 digit number" 
                               value="<?php echo htmlspecialchars($_POST['parent_phone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
