<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Вход</h1>
        <?php if (!empty($_COOKIE['login_error'])): ?>
            <div class="alert alert-danger text-center"><?= $_COOKIE['login_error'] ?></div>
            <?php setcookie('login_error', '', time() - 3600, "/"); ?>
        <?php endif; ?>
        <form action="login.php" method="post" class="p-4 border rounded bg-dark">
            <div class="mb-3">
                <label class="form-label">Логин:</label>
                <input type="text" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
    </div>
</body>
</html>
