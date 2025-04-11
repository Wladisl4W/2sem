<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../../../pass.php");

    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

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
    <title>Вход</title>
</head>
<body>
    <h1>Вход</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Логин: <input type="text" name="login" required></label><br>
        <label>Пароль: <input type="password" name="password" required></label><br>
        <button type="submit">Войти</button>
    </form>
</body>
</html>
