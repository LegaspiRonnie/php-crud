<?php
require_once('./session.php');
require_once('./db.php');
redirectIfAuthenticated();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if($email === '' || $password === '') {
        getStatus('error', 'Email and password are required');
        header('Location: login.php');
        exit;
    }
    
    $sql = "SELECT username, email, password, role FROM php_crud_users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        getStatus('error', 'Email or Password is incorrect');
        header('Location: login.php');
        exit;
    }

    getStatus('success', 'Login success');
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    header('Location: index.php');
    exit;
    
}

?>

<form action="login.php" method="POST" >
    <?php require_once('./alert.php'); ?>
    email: <br>
    <input type="email" name="email" ><br>
    
    Password: <br>
    <input type="password" name="password" required><br>
    <button type="submit">Login</button>
    <a href="register.php">Register here</a>
</form>