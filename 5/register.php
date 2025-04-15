<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../../../pass.php");

    $errors = [];

    if (empty($_POST['FIO']) || strlen($_POST['FIO']) > 150) {
        $errors['FIO'] = 'ФИО не должно быть пустым и не должно превышать 150 символов.';
    } elseif (preg_match('/\d/', $_POST['FIO'])) {
        $errors['FIO'] = 'ФИО не должно содержать цифры.';
    }

    if (empty($_POST['tel']) || !preg_match('/^\+?[0-9]{10,15}$/', $_POST['tel'])) {
        $errors['tel'] = 'Введите корректный номер телефона.';
    }

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email.';
    }

    if (empty($_POST['DR']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['DR'])) {
        $errors['DR'] = 'Введите корректную дату рождения.';
    } else {
        $birthDate = strtotime($_POST['DR']);
        $minDate = strtotime('1900-01-01');
        $currentDate = time();

        if ($birthDate < $minDate) {
            $errors['DR'] = 'Дата рождения не может быть раньше 1900 года.';
        } elseif ($birthDate > $currentDate) {
            $errors['DR'] = 'Дата рождения не может быть в будущем.';
        }
    }

    if (!isset($_POST['sex']) || !in_array($_POST['sex'], ['0', '1'])) {
        $errors['sex'] = 'Выберите корректный пол.';
    }

    if (empty($_POST['bio']) || strlen($_POST['bio']) > 1000) {
        $errors['bio'] = 'Биография не должна быть пустой и не должна превышать 1000 символов.';
    }

    if (empty($_POST['lang']) || !is_array($_POST['lang'])) {
        $errors['lang'] = 'Выберите хотя бы один язык программирования.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['values'] = $_POST;
        header('Location: form.php');
        exit();
    }

    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $login = 'user_' . bin2hex(random_bytes(4));
        $password = bin2hex(random_bytes(4));
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO user_applications (full_name, phone_number, email_address, birth_date, gender, biography, user_login, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $login, $passwordHash
        ]);

        $applicationId = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO users (user_login, password_hash, application_id) VALUES (?, ?, ?)");
        $stmt->execute([$login, $passwordHash, $applicationId]);

        $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
        foreach ($_POST['lang'] as $languageId) {
            $stmt->execute([$applicationId, $languageId]);
        }

        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;

        header('Location: success.php');
        exit();
    } catch (PDOException $e) {
        echo 'Ошибка БД: ' . $e->getMessage();
    }
}
?>
