<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include("../../db.php");
include("../../validation.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = validateFormData($_POST);

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['values'] = $_POST;
        header('Location: form.php');
        exit();
    }

    try {
        $db = getDatabaseConnection();

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
        error_log('Database error: ' . $e->getMessage());
        $_SESSION['errors']['db'] = 'Произошла ошибка при обработке данных. Пожалуйста, попробуйте позже.';
        header('Location: form.php');
        exit();
    }
}
?>
