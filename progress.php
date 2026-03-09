<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'login.html';</script>";
    exit();
}

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "task_manager";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM user WHERE uid = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $userData = $result->fetch_assoc(); 
} else {
    echo "<script>alert('User not found. Please log in again.'); window.location.href = 'login.html';</script>";
    exit();
}

$stmt->close();

// Calculate progress
$task_stmt = $conn->prepare("SELECT status FROM tasks WHERE user_id = ?");
$task_stmt->bind_param("i", $user_id);
$task_stmt->execute();
$task_result = $task_stmt->get_result();

$total_tasks = 0;
$completed_tasks = 0;

while ($task = $task_result->fetch_assoc()) {
    $total_tasks++;
    if (strtolower(trim($task['status'])) === 'completed') {
        $completed_tasks++;
    }
}

$completion_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <title>Progress Page</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="manage.php">Dashboard</a></li>
                
            </ul>
        </nav>
    </header>
        
    <div class="profile-container">
        <h1>Progress Overview for <?php echo htmlspecialchars($userData['uname']); ?>!</h1>
        <hr>
        
        <h2>Completion Progress</h2>
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $completion_percentage; ?>%;"></div>
            </div>
            <p><strong><?php echo $completion_percentage; ?>%</strong> of tasks completed</p>
        </div>

        <div class="statistics">
            <p>Total Tasks: <strong><?php echo $total_tasks; ?></strong></p>
            <p>Completed Tasks: <strong><?php echo $completed_tasks; ?></strong></p>
        </div>
    </div>
</body>
</html>
