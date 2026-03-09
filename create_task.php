<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_name'])) {
    $task_name = $_POST['task_name'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $task_name);

    if ($stmt->execute()) {
        $success_msg = "Task added successfully!";
    } else {
        $error_msg = "Failed to add task.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="create_task.css">
</head>
<body>
    <header>
        <nav>
            <ul>

                <li><a href="manage.php">Dashboard</a></li>
               
            </ul>
        </nav>
    </header>

    <main>
        <h1>Create a New Task</h1>
        <?php
        if (isset($success_msg)) {
            echo "<p class='success'>$success_msg</p>";
        } elseif (isset($error_msg)) {
            echo "<p class='error'>$error_msg</p>";
        }
        ?>
<div class="create-container">
        <form action="create_task.php" method="POST">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" required>
            <button type="submit">Add Task</button>
        </form>
        </div>
    </main>
</body>
</html>
