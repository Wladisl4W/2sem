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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css"> <!-- Подключаем стили -->
</head>
<body>
    <div class="container-narrow">
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2>Регистрация успешна!</h2>
            </div>
            <div class="card-body">
                <p><strong>Ваш логин:</strong></p>
                <p class="highlight text-center"><?= htmlspecialchars($login) ?></p>
                <p><strong>Ваш пароль:</strong></p>
                <p class="highlight text-center"><?= htmlspecialchars($password) ?></p>
                <div class="text-center mt-4">
                    <a href="../index.html" class="btn btn-primary">На главную</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
