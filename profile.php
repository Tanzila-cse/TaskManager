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

$task_stmt = $conn->prepare("SELECT task_name, status, created_at, updated_at FROM tasks WHERE user_id = ?");
$task_stmt->bind_param("i", $user_id);
$task_stmt->execute();
$task_result = $task_stmt->get_result();

$tasks = [
    'pending' => [],
    'completed' => [],
    'deleted' => []
];

while ($task = $task_result->fetch_assoc()) {
    $taskData = [
        'name' => $task['task_name'],
        'created_at' => $task['created_at'],
        'updated_at' => $task['updated_at']
    ];
    
    if (strtolower(trim($task['status'])) === 'pending') {
        $tasks['pending'][] = $taskData;
    } elseif (strtolower(trim($task['status'])) === 'completed') {
        $tasks['completed'][] = $taskData;
    } elseif (strtolower(trim($task['status'])) === 'deleted') {
        $tasks['deleted'][] = $taskData;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <title>User Profile</title>
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
        <h1>Welcome, <?php echo htmlspecialchars($userData['uname']); ?>!</h1>
        <p>Full Name: <?php echo htmlspecialchars($userData['fname']); ?></p>
        <p>Email: <?php echo htmlspecialchars($userData['email']); ?></p>
        <p>Username: <?php echo htmlspecialchars($userData['uname']); ?></p>
        <p>Password: <?php echo htmlspecialchars($userData['password']); ?></p>
        <hr>
        
        <h2>Pending Tasks</h2>
        <ul>
            <?php 
            if (!empty($tasks['pending'])) {
                foreach ($tasks['pending'] as $task) {
                    echo "<li>" . htmlspecialchars($task['name']) . 
                         "<br><small>Created At: " . htmlspecialchars($task['created_at']) . 
                         "<br>Updated At: " . htmlspecialchars($task['updated_at']) . "</small></li>";
                }
            } else {
                echo "<li>No pending tasks available.</li>";
            }
            ?>
        </ul>

        <h2>Completed Tasks</h2>
        <ul>
            <?php 
            if (!empty($tasks['completed'])) {
                foreach ($tasks['completed'] as $task) {
                    echo "<li>" . htmlspecialchars($task['name']) . 
                         "<br><small>Created At: " . htmlspecialchars($task['created_at']) . 
                         "<br>Updated At: " . htmlspecialchars($task['updated_at']) . "</small></li>";
                }
            } else {
                echo "<li>No completed tasks available.</li>";
            }
            ?>
        </ul>

        <h2>Deleted Tasks</h2>
        <ul>
            <?php 
            if (!empty($tasks['deleted'])) {
                foreach ($tasks['deleted'] as $task) {
                    echo "<li>" . htmlspecialchars($task['name']) . 
                         "<br><small>Created At: " . htmlspecialchars($task['created_at']) . 
                         "<br>Updated At: " . htmlspecialchars($task['updated_at']) . "</small></li>";
                }
            } else {
                echo "<li>No deleted tasks available.</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
