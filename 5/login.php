<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

require_once 'db.php';

if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Вход</title>
</head>
<body>
    <div class="container">
        <h1>Вход</h1>
        <form action="" method="POST">
            <label for="login">Логин:</label>
            <input type="text" name="login" id="login" required />

            <label for="pass">Пароль:</label>
            <input type="password" name="pass" id="pass" required />

            <input type="submit" value="Войти" />
        </form>
    </div>
</body>
</html>
<?php
} else {
    $stmt = $db->prepare("SELECT * FROM applications WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['pass'], $user['password_hash'])) {
        $_SESSION['login'] = $user['login'];
        $_SESSION['uid'] = $user['id_app'];
        header('Location: index.php');
        exit();
    } else {
        echo "Неверный логин или пароль.";
    }
}
?>