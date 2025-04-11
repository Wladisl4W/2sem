<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../../../pass.php");

    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Генерация логина и пароля
        $login = 'user_' . bin2hex(random_bytes(4));
        $password = bin2hex(random_bytes(4));
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Сохранение данных формы
        $stmt = $db->prepare("INSERT INTO user_applications (full_name, phone_number, email_address, birth_date, gender, biography, user_login, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $login, $passwordHash
        ]);

        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;

        header('Location: success.php');
        exit();
    } catch (PDOException $e) {
        echo 'Ошибка БД: ' . $e->getMessage();
    }
}
?>
