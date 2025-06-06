<?php
session_start();
include("../../db.php");

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['login']) || empty($_POST['password'])) {
        $error = 'Пожалуйста, заполните оба поля.';
    } else {
        try {
            $db = getDatabaseConnection();

            $stmt = $db->prepare("SELECT user_id, password_hash FROM users WHERE user_login = ?");
            $stmt->execute([$_POST['login']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($_POST['password'], $user['password_hash'])) {
                $_SESSION['user_id'] = intval($user['user_id']);
                header('Location: edit.php');
                exit();
            } else {
                $error = 'Неверный логин или пароль.';
            }
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $error = 'Ошибка базы данных. Пожалуйста, попробуйте позже.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-narrow">
        <div class="header-box">Вход</div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" class="border p-4 rounded bg-dark">
            <div class="mb-3">
                <label for="login" class="form-label">Логин:</label>
                <input type="text" id="login" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Войти</button>
                <a href="form.php" class="btn btn-secondary">Регистрация</a>
            </div>
        </form>
    </div>
</body>
</html>
