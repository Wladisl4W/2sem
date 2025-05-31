<?php
session_start();
include("db.php");

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
                header('Location: ../edit.php');
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
