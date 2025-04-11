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
            background-color: #0b0b0b; /* Ещё более тёмный фон */
            color: #ffffff;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .container {
            max-width: 400px;
        }
        .card {
            background-color: #141414; /* Ещё более тёмный цвет формы */
            border: 1px solid #444;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .card-header {
            background-color: #4caf50; /* Зеленый фон */
            color: #ffffff;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .card-body {
            text-align: center;
            padding: 20px;
        }
        .card-body p {
            font-size: 1.2rem;
            margin-bottom: 0.3rem; /* Уменьшенный отступ между текстом и значением */
            color: #ffffff;
        }
        .card-body .block-spacing {
            margin-bottom: 1rem; /* Больший отступ между блоками */
        }
        .btn-primary {
            background-color: #d63384;
            border: none;
            font-size: 1rem;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #b82c6e;
        }
        .highlight {
            color: #ffffff;
            font-weight: bold;
            font-size: 1.3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Регистрация успешна!
            </div>
            <div class="card-body">
                <div class="block-spacing">
                    <p>Ваш логин:</p>
                    <p class="highlight"><?= htmlspecialchars($_SESSION['login']) ?></p>
                </div>
                <div class="block-spacing">
                    <p>Ваш пароль:</p>
                    <p class="highlight"><?= htmlspecialchars($_SESSION['password']) ?></p>
                </div>
                <a href="login.php" class="btn btn-primary w-100 mt-3">Войти</a>
            </div>
        </div>
    </div>
    <?php session_destroy(); ?>
</body>
</html>
