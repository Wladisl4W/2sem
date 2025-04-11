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

        // Сохранение данных формы в таблицу user_applications
        $stmt = $db->prepare("INSERT INTO user_applications (full_name, phone_number, email_address, birth_date, gender, biography, user_login, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $login, $passwordHash
        ]);

        // Получение ID заявки
        $applicationId = $db->lastInsertId();

        // Сохранение данных в таблицу users
        $stmt = $db->prepare("INSERT INTO users (user_login, password_hash, application_id) VALUES (?, ?, ?)");
        $stmt->execute([$login, $passwordHash, $applicationId]);

        // Сохранение выбранных языков программирования
        if (!empty($_POST['lang'])) {
            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($_POST['lang'] as $languageId) {
                $stmt->execute([$applicationId, $languageId]);
            }
        }

        // Сохранение логина и пароля в сессии
        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;

        header('Location: success.php');
        exit();
    } catch (PDOException $e) {
        echo 'Ошибка БД: ' . $e->getMessage();
    }
}
?>
