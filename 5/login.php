<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../../../pass.php");
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $db->prepare("SELECT application_id, password_hash FROM user_applications WHERE user_login = ?");
        $stmt->execute([$_POST['login']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['application_id'];
            header('Location: dashboard.php');
            exit();
        } else {
            setcookie('login_error', 'Неверный логин или пароль.', 0, "/");
            header('Location: login_form.php');
            exit();
        }
    } catch (PDOException $e) {
        setcookie('DBerror', 'Ошибка БД: ' . $e->getMessage(), 0, "/");
        header('Location: login_form.php');
        exit();
    }
}
?>
