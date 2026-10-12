<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdminLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decision'])) {
    $request_id = (int)($_POST['request_id'] ?? 0);
    $decision = $_POST['decision'] ?? '';

    if ($request_id <= 0 || !in_array($decision, ['Approved', 'Rejected'], true)) {
        $error = 'Invalid request action.';
    } else {
        $result = processAllocationRequest($request_id, $decision);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

$requests = getAllocationRequests();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocation Requests</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Hostel Admin</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="rooms.php">Room Management</a></li>
                <li><a href="requests.php" class="active">Requests</a></li>
                <li><a href="../auth/logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Room Allocation Requests</h1>
            <p>Approve or reject student room requests.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Enrollment</th>
                            <th>Requested Room</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6">No requests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['enrollment_no']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($request['room_number']); ?>
                                        (<?php echo htmlspecialchars($request['block']); ?>)
                                    </td>
                                    <td><?php echo date('d-m-Y', strtotime($request['requested_on'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($request['status']); ?>"><?php echo htmlspecialchars($request['status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($request['status'] === 'Pending'): ?>
                                            <div class="action-group">
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo (int)$request['id']; ?>">
                                                    <button type="submit" name="decision" value="Approved" class="btn-primary">Approve</button>
                                                </form>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo (int)$request['id']; ?>">
                                                    <button type="submit" name="decision" value="Rejected" class="btn-danger">Reject</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span><?php echo htmlspecialchars($request['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
