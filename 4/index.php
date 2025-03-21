<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = FALSE;

    // ФИО
    if (strlen($_POST['FIO']) > 150) {
        setcookie('fio_error', 'ФИО слишком длинное.', 0, "/");
        $error = TRUE;
    } elseif (preg_match('~[0-9]+~', $_POST['FIO'])) {
        setcookie('fio_error', 'ФИО не должно содержать цифры.', 0, "/");
        $error = TRUE;
    } else {
        setcookie('fio_error', '', time() - 3600, "/");
        setcookie('fio_value', $_POST['FIO'], time() + 31556926, "/");
    }

    // Телефон
    if (!preg_match('/^\+7\d{10}$/', $_POST['tel'])) {
        setcookie('tel_error', 'Формат: +7XXXXXXXXXX (11 цифр).', 0, "/");
        $error = TRUE;
    } else {
        setcookie('tel_error', '', time() - 3600, "/");
        setcookie('tel_value', $_POST['tel'], time() + 31556926, "/");
    }

    // Email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', 'Некорректный email.', 0, "/");
        $error = TRUE;
    } else {
        setcookie('email_error', '', time() - 3600, "/");
        setcookie('email_value', $_POST['email'], time() + 31556926, "/");
    }

    // Дата рождения
    $year = (int)substr($_POST['DR'], 0, 4);
    if ($year < 1800 || $year > date("Y")) {
        setcookie('dr_error', 'Некорректная дата.', 0, "/");
        $error = TRUE;
    } else {
        setcookie('dr_error', '', time() - 3600, "/");
        setcookie('dr_value', $_POST['DR'], time() + 31556926, "/");
    }

    // Пол
    if (!in_array($_POST['sex'], ['0', '1'])) {
        setcookie('sex_error', 'Некорректное значение.', 0, "/");
        $error = TRUE;
    } else {
        setcookie('sex_error', '', time() - 3600, "/");
        setcookie('sex_value', $_POST['sex'], time() + 31556926, "/");
    }

    // Языки программирования
    if (empty($_POST['lang'])) {
        setcookie('lang_error', 'Выберите хотя бы один язык.', 0, "/");
        $error = TRUE;
    } else {
        setcookie('lang_error', '', time() - 3600, "/");
        setcookie('lang_value', implode('|', $_POST['lang']), time() + 31556926, "/");
    }

    // Биография
    if (strlen($_POST['bio']) > 200) {
        setcookie('bio_error', 'Максимум 200 символов.', 0, "/");
        $error = TRUE;
    } else {
        setcookie('bio_error', '', time() - 3600, "/");
        setcookie('bio_value', $_POST['bio'], time() + 31556926, "/");
    }

    // Если есть ошибки - редирект
    if ($error) {
        header('Location: index.php');
        exit();
    }

    // Сохранение в БД
    include("../../../pass.php");
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $db->prepare("INSERT INTO applications (FIO, tel, email, DR, sex, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio']]);

        $id_app = $db->lastInsertId();
        foreach ($_POST['lang'] as $lang) {
            $stmt = $db->prepare("INSERT INTO app_langs (id_app, id_lang) VALUES (?, ?)");
            $stmt->execute([$id_app, $lang]);
        }

        setcookie('save', 'Успешно!', time() + 10, "/");
        header('Location: index.php');
        exit();

    } catch (PDOException $e) {
        setcookie('DBerror', 'Ошибка БД: ' . $e->getMessage(), 0, "/");
        header('Location: index.php');
        exit();
    }
}

include("form.php");
?>
