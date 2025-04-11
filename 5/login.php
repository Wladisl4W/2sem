<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../../../pass.php");

    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Проверка логина и пароля в таблице users
        $stmt = $db->prepare("SELECT user_id, password_hash FROM users WHERE user_login = ?");
        $stmt->execute([$_POST['login']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            header('Location: edit.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль.';
        }
    } catch (PDOException $e) {
        $error = 'Ошибка БД: ' . $e->getMessage();
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
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
        }
        .form-control, .btn {
            background-color: #1e1e1e;
            color: #ffffff;
            border: 1px solid #444;
        }
        .form-control:focus {
            border-color: #d63384;
            box-shadow: 0 0 5px #d63384;
        }
        .btn-primary {
            background-color: #d63384;
            border: none;
        }
        .btn-primary:hover {
            background-color: #b82c6e;
        }
        .btn-secondary {
            background-color: #444;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #666;
        }
        .alert {
            background-color: #333;
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Вход</h1>
        <?php if (!empty($error)): ?>
            <div class="alert text-center"><?= htmlspecialchars($error) ?></div>
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
