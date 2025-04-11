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
    <title>Успешная регистрация</title>
</head>
<body>
    <h1>Регистрация успешна!</h1>
    <p>Ваш логин: <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></p>
    <p>Ваш пароль: <strong><?= htmlspecialchars($_SESSION['password']) ?></strong></p>
    <a href="login.php">Войти</a>
    <?php session_destroy(); ?>
</body>
</html>
