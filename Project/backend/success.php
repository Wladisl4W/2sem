<?php
session_start();
if (empty($_SESSION['login']) || empty($_SESSION['password'])) {
    header('Location: ../index.html'); // Обновляем путь к index.html
    exit();
}

$login = $_SESSION['login'];
$password = $_SESSION['password'];

// Удаляем пароль из сессии сразу после его извлечения
unset($_SESSION['password']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Успешная регистрация</title>
    <link rel="stylesheet" href="success_style.css"> <!-- Подключаем новый файл стилей -->
</head>
<body>
    <div class="container-narrow">
        <div class="card">
            <div class="card-header">
                Регистрация успешна!
            </div>
            <div class="card-body">
                <p><strong>Ваш логин:</strong></p>
                <p class="highlight"><?= htmlspecialchars($login) ?></p>
                <p><strong>Ваш пароль:</strong></p>
                <p class="highlight"><?= htmlspecialchars($password) ?></p>
                <a href="../index.html" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </div>
</body>
</html>
