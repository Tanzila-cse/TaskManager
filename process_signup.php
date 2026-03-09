<?php
$host = "localhost";
$dbname = "task_manager";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


$fname = trim($_POST['name']);
$email = trim($_POST['email']);
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$confirmPassword = trim($_POST['confirmPassword']);

$errors = [];


if (empty($fname)) {
    $errors[] = "Full Name cannot be empty!";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address!";
}
if (!preg_match('/^[a-zA-Z0-9_]{3,}$/', $username)) {
    $errors[] = "Username must be at least 3 characters long and can only contain letters, numbers, and underscores!";
}
if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $password)) {
    $errors[] = "Password must include at least one uppercase letter, one lowercase letter, one number, and one special character!";
}
if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match!";
}

if (empty($errors)) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email OR uname = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "An account with this email or username already exists!";
    }
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
} else {
    
    $stmt = $conn->prepare("INSERT INTO user (fname, email, uname, password) VALUES (:fname, :email, :username, :password)");
    $stmt->execute([
        'fname' => $fname,
        'email' => $email,
        'username' => $username,
        'password' => $password 
    ]);

    echo "<p style='color: green;'>Account created successfully! <a href='login.html'>Login here</a></p>";
}
?>
