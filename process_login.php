<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "task_manager";


$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT uid, password, uname FROM user WHERE uname = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['username'] = $user['uname']; 
            $_SESSION['user_id'] = $user['uid'];
            header("Location: manage.php");
            exit;
        } else {
            echo "<script>alert('Invalid username or password'); window.location.href = 'login.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href = 'login.html';</script>";
    }
}
$conn->close();
?>
