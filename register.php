<?php
require 'db.php';
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($full === '' || $username === '' || strlen($password) < 6) {
        $error = 'Please complete all fields. Password must be at least 6 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users(full_name,username,password_hash) VALUES (?,?,?)');
            $stmt->execute([$full, $username, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['full_name'] = $full;
            header('Location: index.php'); exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'Username already exists.' : 'Registration failed.';
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register | SmartFix-AI</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-body"><form class="auth-card" method="post"><h1>SMARTFIX-AI</h1><h2>Create Account</h2><?php if($error): ?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif; ?><label>Full Name</label><input name="full_name" required><label>Username</label><input name="username" required><label>Password</label><input type="password" name="password" minlength="6" required><button class="btn" type="submit">Register</button><p>Already registered? <a href="login.php">Login</a></p></form></body></html>
