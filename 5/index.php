<?php
session_start();

// Подключение к базе данных
include("../../../pass.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Обработка GET-запроса (вывод формы или сообщений)
    $messages = [];

    if (!empty($_SESSION['success_message'])) {
        $messages[] = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }

    if (!empty($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    } else {
        $errors = [];
    }

    include 'form.php';
} else {
    // Обработка POST-запроса (проверка данных и сохранение)
    $errors = [];

    if (empty($_POST['FIO'])) {
        $errors['FIO'] = 'Введите ФИО.';
    }
    if (empty($_POST['tel'])) {
        $errors['tel'] = 'Введите номер телефона.';
    }
    if (empty($_POST['email'])) {
        $errors['email'] = 'Введите email.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ./');
        exit();
    }

    $FIO = htmlspecialchars($_POST['FIO']);
    $tel = htmlspecialchars($_POST['tel']);
    $email = htmlspecialchars($_POST['email']);
    $DR = htmlspecialchars($_POST['DR']);
    $sex = htmlspecialchars($_POST['sex']);
    $bio = htmlspecialchars($_POST['bio']);

    if (!isset($_SESSION['user_id'])) {
        // Новый пользователь: генерация логина и пароля
        $login = 'user' . uniqid();
        $password = substr(md5(time()), 0, 8); // Пример генерации пароля
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Сохранение данных в базу
        $stmt = $pdo->prepare("INSERT INTO applications (FIO, tel, email, DR, sex, bio, login, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$FIO, $tel, $email, $DR, $sex, $bio, $login, $password_hash]);

        // Получение ID только что созданного пользователя
        $user_id = $pdo->lastInsertId();

        // Сохранение выбранных языков программирования
        $languages = isset($_POST['languages']) ? $_POST['languages'] : [];
        foreach ($languages as $lang_id) {
            $stmt = $pdo->prepare("INSERT INTO app_langs (id_app, id_lang) VALUES (?, ?)");
            $stmt->execute([$user_id, $lang_id]);
        }

        // Сообщение пользователю
        $_SESSION['success_message'] = "Ваши данные успешно сохранены. Логин: $login, Пароль: $password";
    } else {
        // Существующий пользователь: обновление данных
        $stmt = $pdo->prepare("UPDATE applications SET FIO = ?, tel = ?, email = ?, DR = ?, sex = ?, bio = ? WHERE id_app = ?");
        $stmt->execute([$FIO, $tel, $email, $DR, $sex, $bio, $_SESSION['user_id']]);

        // Обновление выбранных языков программирования
        $stmt = $pdo->prepare("DELETE FROM app_langs WHERE id_app = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $languages = isset($_POST['languages']) ? $_POST['languages'] : [];
        foreach ($languages as $lang_id) {
            $stmt = $pdo->prepare("INSERT INTO app_langs (id_app, id_lang) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $lang_id]);
        }

        $_SESSION['success_message'] = "Ваши данные успешно обновлены.";
    }

    header('Location: ./');
    exit();
}
?>