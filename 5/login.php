<?php
session_start();

// Подключение к базе данных
include("../../../pass.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Вывод формы входа
    include 'login_form.php';
} else {
    // Обработка входа
    $login = $_POST['login'] ?? '';
    $password = $_POST['pass'] ?? '';

    if (empty($login) || empty($password)) {
        $_SESSION['login_error'] = 'Введите логин и пароль.';
        header('Location: login.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM applications WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Успешная авторизация
        $_SESSION['user_id'] = $user['id_app'];
        $_SESSION['login'] = $user['login'];
        header('Location: ./');
        exit();
    } else {
        // Ошибка авторизации
        $_SESSION['login_error'] = 'Неверный логин или пароль.';
        header('Location: login.php');
        exit();
    }
}
?>