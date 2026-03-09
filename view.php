<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database Connection
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "task_manager";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch pending tasks for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, task_name FROM tasks WHERE user_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view.css">
    <title>View Tasks - Task Manager</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                
                <li><a href="manage.php">Dashboard</a></li>
                
            </ul>
        </nav>
    </header>

    <div class="tasks-container">
        <h1>Pending Tasks</h1>
        <div class="task-list">
            <?php if (empty($tasks)): ?>
                <p>No pending tasks available.</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task" data-id="<?= htmlspecialchars($task['id']); ?>">
                        <p class="task-title"><?= htmlspecialchars($task['task_name']); ?></p>
                        <div class="task-actions">
                            <button class="complete-btn"> Completed</button>
                            <button class="delete-btn">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    

    <script>
        document.querySelectorAll('.complete-btn').forEach(button => {
            button.addEventListener('click', () => handleAction(button, 'complete'));
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => handleAction(button, 'delete'));
        });

        function handleAction(button, action) {
            const taskDiv = button.closest('.task');
            const taskId = taskDiv.dataset.id;

            fetch('update_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${taskId}&action=${action}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    taskDiv.remove();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>

