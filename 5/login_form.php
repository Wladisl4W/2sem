<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
</head>
<body>

<?php if (!empty($_SESSION['login_error'])): ?>
    <p style="color: red;"><?= $_SESSION['login_error'] ?></p>
    <?php unset($_SESSION['login_error']); ?>
<?php endif; ?>

<form action="login.php" method="post">
    <label for="login">Логин:</label>
    <input type="text" name="login" id="login"><br><br>
    <label for="pass">Пароль:</label>
    <input type="password" name="pass" id="pass"><br><br>
    <input type="submit" value="Войти">
</form>

</body>
</html>