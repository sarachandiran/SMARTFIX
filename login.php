<?php
require 'db.php';
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT user_id, full_name, password_hash
         FROM users
         WHERE username = ?'
    );

    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];

        header('Location: index.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | SMARTFIX</title>

    <link rel="stylesheet" href="style.css">

    <style>
        body.auth-body {
            min-height: 100vh;
            margin: 0;
            padding: 24px 16px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(
                    circle at top,
                    rgba(56, 189, 248, 0.12),
                    transparent 55%
                ),
                #0f172a;
            font-family: Arial, sans-serif;
        }

        .auth-card.smartfix-login {
            width: 100%;
            max-width: 440px;
            box-sizing: border-box;
            padding: 28px 32px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .smartfix-login .login-pc {
            display: block;
            width: 180px;
            max-width: 100%;
            height: 170px;
            object-fit: contain;
            margin: 0 auto 12px;
            filter: drop-shadow(
                0 0 12px rgba(56, 189, 248, 0.25)
            );
        }

        .smartfix-login .login-title {
            margin: 0 0 10px;
            color: #38bdf8;
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 2px;
            text-align: center;
        }

        .smartfix-login .login-subtitle {
            margin: 0 0 24px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.7;
            text-align: center;
        }

        .smartfix-login .subtitle-white {
            color: #f8fafc;
        }

        .smartfix-login .subtitle-gold {
            color: #fbbf24;
        }

        .smartfix-login h2 {
            margin: 0 0 20px;
            padding-top: 20px;
            border-top: 1px solid #334155;
            color: #f8fafc;
            font-size: 22px;
            text-align: center;
        }

        .smartfix-login label {
            display: block;
            margin: 14px 0 7px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 600;
        }

        .smartfix-login input {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            margin: 0;
            background: #0f172a;
            color: #f8fafc;
            border: 1px solid #475569;
            border-radius: 9px;
            font: inherit;
        }

        .smartfix-login input:focus {
            outline: 2px solid #38bdf8;
            outline-offset: 2px;
            border-color: #38bdf8;
        }

        .smartfix-login .login-button {
            display: block;
            width: 100%;
            margin-top: 22px;
            padding: 13px;
            background: #38bdf8;
            color: #082f49;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
        }

        .smartfix-login .login-button:hover {
            background: #7dd3fc;
        }

        .smartfix-login .login-button:focus-visible,
        .smartfix-login a:focus-visible {
            outline: 2px solid #fbbf24;
            outline-offset: 3px;
        }

        .smartfix-login .login-error {
            margin-bottom: 16px;
            padding: 12px;
            background: #451a23;
            color: #fecdd3;
            border: 1px solid #fb7185;
            border-radius: 9px;
            font-size: 14px;
        }

        .smartfix-login .register-link {
            margin: 20px 0 0;
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
        }

        .smartfix-login .register-link a {
            color: #38bdf8;
            font-weight: 700;
        }

        @media (max-width: 480px) {
            .auth-card.smartfix-login {
                padding: 24px 20px;
            }

            .smartfix-login .login-title {
                font-size: 32px;
            }

            .smartfix-login .login-subtitle {
                font-size: 12px;
            }
        }
    </style>
</head>

<body class="auth-body">

    <form class="auth-card smartfix-login" method="post">

        <img
            class="login-pc"
            src="assets/pc_main.png"
            alt="Komputer SMARTFIX"
        >

        <h1 class="login-title">
            SMARTFIX
        </h1>

        <p class="login-subtitle" lang="ms">
            <span class="subtitle-white">
                SEBAGAI SIMULATOR INTERAKTIF
            </span>
            <br>
            <span class="subtitle-gold">
                TROUBLESHOOTING KOMPUTER
            </span>
        </p>

        <h2>Student Login</h2>

        <?php if ($error): ?>
            <div class="login-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <label for="username">Username</label>
        <input
            id="username"
            name="username"
            type="text"
            placeholder="Enter your username"
            autocomplete="username"
            required
            autofocus
        >

        <label for="password">Password</label>
        <input
            id="password"
            name="password"
            type="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
        >

        <button class="login-button" type="submit">
            Login
        </button>

        <p class="register-link">
            New user?
            <a href="register.php">Create account</a>
        </p>

    </form>

</body>
</html>