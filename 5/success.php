<?php
session_start();
if (empty($_SESSION['login']) || empty($_SESSION['password'])) {
    header('Location: register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Успешная регистрация</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
        }
        .card {
            background-color: #1e1e1e;
            border: 1px solid #444;
        }
        .card-header {
            background-color: #d63384;
            color: #ffffff;
            text-align: center;
        }
        .card-body {
            text-align: center;
        }
        .card-body p {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #d63384;
            border: none;
        }
        .btn-primary:hover {
            background-color: #b82c6e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Регистрация успешна!</h2>
            </div>
            <div class="card-body">
                <p>Ваш логин:</p>
                <p><strong style="color: #ffffff;"><?= htmlspecialchars($_SESSION['login']) ?></strong></p>
                <p>Ваш пароль:</p>
                <p><strong style="color: #ffffff;"><?= htmlspecialchars($_SESSION['password']) ?></strong></p>
                <a href="login.php" class="btn btn-primary w-100">Войти</a>
            </div>
        </div>
    </div>
    <?php session_destroy(); ?>
</body>
</html>
