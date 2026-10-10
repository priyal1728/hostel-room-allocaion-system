<?php
echo "<h2>System Check</h2>";

// Check if config.php works
require_once 'includes/config.php';

echo "✓ Database Connected<br>";

// Check tables
$tables = ['students', 'rooms', 'room_allocations'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' NOT found<br>";
    }
}

echo "<br><strong>Setup incomplete? Run this:</strong><br>";
echo "<a href='db/setup.php' style='background: #667eea; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px;'>Click to Setup Database</a>";
?>
