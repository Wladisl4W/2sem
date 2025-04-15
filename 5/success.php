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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-narrow">
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
