<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$student = getStudentById($_SESSION['student_id']);
$room_allocation = getStudentRoomAllocation($_SESSION['student_id']);
$error = '';
$success = '';

// Check if student already has allocation
if ($room_allocation) {
    header("Location: dashboard.php");
    exit();
}

// Fetch available rooms
$query = "SELECT * FROM rooms WHERE status = 'Available' AND current_occupancy < capacity ORDER BY block, room_number";
$result = mysqli_query($conn, $query);
$available_rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle room request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_room'])) {
    $room_id = $_POST['room_id'] ?? '';
    
    if (empty($room_id)) {
        $error = "Please select a room!";
    } else {
        // Check if room still available
        $check_query = "SELECT * FROM rooms WHERE id = ? AND current_occupancy < capacity";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $room_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($check_result) == 0) {
            $error = "Selected room is no longer available!";
        } else {
            $allocation_date = date('Y-m-d');
            
            $insert_query = "INSERT INTO room_allocations (student_id, room_id, allocation_date, status) VALUES (?, ?, ?, 'Active')";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "iis", $_SESSION['student_id'], $room_id, $allocation_date);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                // Update room occupancy
                $update_query = "UPDATE rooms SET current_occupancy = current_occupancy + 1 WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "i", $room_id);
                mysqli_stmt_execute($update_stmt);
                
                $success = "Room allocated successfully! Redirecting...";
                header("refresh:2;url=dashboard.php");
            } else {
                $error = "Error allocating room: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Request - Hostel Management</title>
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
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="room-request.php" class="active">Room Request</a></li>
                <li><a href="../auth/logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Request a Room</h1>
            <p>Select and apply for an available hostel room</p>
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

        <?php if (count($available_rooms) > 0): ?>
            <div class="profile-section">
                <h2>Available Rooms</h2>
                <form method="POST" class="room-request-form">
                    <div class="rooms-grid">
                        <?php foreach ($available_rooms as $room): ?>
                            <div class="room-card">
                                <div class="room-info">
                                    <h4>Room <?php echo htmlspecialchars($room['room_number']); ?></h4>
                                    <p><strong>Block:</strong> <?php echo htmlspecialchars($room['block']); ?></p>
                                    <p><strong>Floor:</strong> <?php echo htmlspecialchars($room['floor']); ?></p>
                                    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> students</p>
                                    <p><strong>Occupied:</strong> <?php echo htmlspecialchars($room['current_occupancy']); ?>/<?php echo htmlspecialchars($room['capacity']); ?></p>
                                </div>
                                <label class="room-radio">
                                    <input type="radio" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
                                    <span>Select This Room</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" name="request_room" class="btn-primary" style="margin-top: 25px;">Confirm Allocation</button>
                </form>
            </div>
        <?php else: ?>
            <div class="profile-section">
                <div class="no-allocation">
                    <h3>No Rooms Available</h3>
                    <p>All rooms are currently fully occupied. Please check back later.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .room-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background: #f9f9f9;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .room-card:hover {
            border-color: #667eea;
            background: #f0f4ff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .room-card input[type="radio"]:checked + span {
            color: #667eea;
            font-weight: 600;
        }

        .room-info {
            margin-bottom: 15px;
        }

        .room-info h4 {
            color: #173b67;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .room-info p {
            font-size: 13px;
            margin-bottom: 8px;
            color: #555;
        }

        .room-radio {
            display: flex;
            align-items: center;
            cursor: pointer;
            gap: 10px;
            font-weight: 500;
            color: #666;
        }

        .room-radio input {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            .rooms-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
