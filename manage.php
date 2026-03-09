<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html"); 
    exit;
}
$fullname = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manage.css">
    <title>Task Manager - Dashboard</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                
                <li><a href="manage.php">Dashboard</a></li>
                
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($fullname); ?>!</h1>
            <p>Manage your tasks and stay organized!</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Task Overview</h3>
                <p>Quick glance at your pending tasks</p>
                <a href="view.php" class="cta-button">View Tasks</a>
            </div>
            <div class="card">
                <h3>Create New Task</h3>
                <p>Add new tasks </p>
                <a href="create_task.php" class="cta-button">Create Task</a>
            </div>
            <div class="card">
                <h3>View Progress</h3>
                <p>Track your task progress</p>
                <a href="progress.php" class="cta-button">View Progress</a>
            </div>
            <div class="card">
                <h3>Profile Settings</h3>
                <p>All about you</p>
                <a href="profile.php" class="cta-button">Go to Profile</a>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 Task Manager | All Rights Reserved</p>
        </footer>
    </div>
</body>
</html>

