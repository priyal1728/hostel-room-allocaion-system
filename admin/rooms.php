<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdminLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_room'])) {
        $room_number = trim($_POST['room_number'] ?? '');
        $block = trim($_POST['block'] ?? '');
        $floor = trim($_POST['floor'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $status = $_POST['status'] ?? 'Available';

        if ($room_number === '' || $block === '' || $floor === '' || $capacity <= 0) {
            $error = 'Please fill all required room details.';
        } else {
            $query = "INSERT INTO rooms (room_number, block, floor, capacity, current_occupancy, status) VALUES (?, ?, ?, ?, 0, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sssis', $room_number, $block, $floor, $capacity, $status);

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Room added successfully.';
            } else {
                $error = 'Error adding room: ' . mysqli_error($conn);
            }
        }
    }

    if (isset($_POST['update_room'])) {
        $id = (int)($_POST['room_id'] ?? 0);
        $room_number = trim($_POST['room_number'] ?? '');
        $block = trim($_POST['block'] ?? '');
        $floor = trim($_POST['floor'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $status = $_POST['status'] ?? 'Available';
        $current_occupancy = (int)($_POST['current_occupancy'] ?? 0);

        if ($id <= 0 || $room_number === '' || $block === '' || $floor === '' || $capacity <= 0) {
            $error = 'Invalid room details.';
        } else {
            $current_occupancy = min($current_occupancy, $capacity);
            $query = "UPDATE rooms SET room_number=?, block=?, floor=?, capacity=?, current_occupancy=?, status=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sssiisi', $room_number, $block, $floor, $capacity, $current_occupancy, $status, $id);

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Room updated successfully.';
            } else {
                $error = 'Error updating room: ' . mysqli_error($conn);
            }
        }
    }

    if (isset($_POST['delete_room'])) {
        $id = (int)($_POST['room_id'] ?? 0);
        $query = "DELETE FROM rooms WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = 'Room deleted successfully.';
        } else {
            $error = 'Error deleting room: ' . mysqli_error($conn);
        }
    }
}

$rooms = getAllRooms();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
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
                <li><a href="rooms.php" class="active">Room Management</a></li>
                <li><a href="requests.php">Requests</a></li>
                <li><a href="../auth/logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Room Management</h1>
            <p>Add, update and remove hostel rooms.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="panel">
            <h2>Add New Room</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Room Number</label>
                        <input type="text" name="room_number" required>
                    </div>
                    <div class="form-group">
                        <label>Block</label>
                        <input type="text" name="block" required>
                    </div>
                    <div class="form-group">
                        <label>Floor</label>
                        <input type="text" name="floor" required>
                    </div>
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" name="capacity" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="Available">Available</option>
                            <option value="Full">Full</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="save_room" class="btn-primary">Add Room</button>
            </form>
        </div>

        <div class="panel" style="margin-top: 28px;">
            <h2>Existing Rooms</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Room No</th>
                            <th>Block</th>
                            <th>Floor</th>
                            <th>Capacity</th>
                            <th>Occupied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?php echo (int)$room['id']; ?></td>
                                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($room['block']); ?></td>
                                <td><?php echo htmlspecialchars($room['floor']); ?></td>
                                <td><?php echo (int)$room['capacity']; ?></td>
                                <td><?php echo (int)$room['current_occupancy']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($room['status']); ?>">
                                        <?php echo htmlspecialchars($room['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="room_id" value="<?php echo (int)$room['id']; ?>">
                                            <input type="hidden" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>">
                                            <input type="hidden" name="block" value="<?php echo htmlspecialchars($room['block']); ?>">
                                            <input type="hidden" name="floor" value="<?php echo htmlspecialchars($room['floor']); ?>">
                                            <input type="hidden" name="capacity" value="<?php echo (int)$room['capacity']; ?>">
                                            <input type="hidden" name="current_occupancy" value="<?php echo (int)$room['current_occupancy']; ?>">
                                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($room['status']); ?>">
                                            <button type="submit" name="update_room" class="btn-secondary">Edit</button>
                                        </form>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this room?');">
                                            <input type="hidden" name="room_id" value="<?php echo (int)$room['id']; ?>">
                                            <button type="submit" name="delete_room" class="btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
