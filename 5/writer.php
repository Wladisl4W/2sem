<?php
require_once __DIR__ . '/session.php';
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = false;

    // Валидация ФИО
    if (empty($_POST['full_name'])) {
        setcookie('fio_error', 'Введите ФИО.');
        $error = true;
    } elseif (strlen($_POST['full_name']) > 150) {
        setcookie('fio_error', 'ФИО слишком длинное.');
        $error = true;
    } elseif (preg_match('~[0-9]+~', $_POST['full_name'])) {
        setcookie('fio_error', 'ФИО не должно содержать цифры.');
        $error = true;
    } else {
        setcookie('fio_error', '', strtotime("-1 day"));
    }
    setcookie('fio_value', $_POST['full_name'], strtotime('+1 year'));

    // Валидация телефона
    if (!preg_match('/^[0-9]{10}$/', $_POST['phone_number'])) {
        setcookie('tel_error', 'Номер телефона должен содержать ровно 10 цифр.');
        $error = true;
    } else {
        setcookie('tel_error', '', strtotime("-1 day"));
    }
    setcookie('tel_value', $_POST['phone_number'], strtotime('+1 year'));

    // Валидация email
    if (!filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', 'Введите корректный email.');
        $error = true;
    } else {
        setcookie('email_error', '', strtotime("-1 day"));
    }
    setcookie('email_value', $_POST['email_address'], strtotime('+1 year'));

    // Валидация даты рождения
    $year = (int) substr($_POST['birth_date'], 0, 4);
    if ($year < 1900) {
        setcookie('dr_error', 'Год рождения не может быть меньше 1900.');
        $error = true;
    } elseif ($year > date('Y')) {
        setcookie('dr_error', 'Год рождения не может быть в будущем.');
        $error = true;
    } else {
        setcookie('dr_error', '', strtotime("-1 day"));
    }
    setcookie('dr_value', $_POST['birth_date'], strtotime('+1 year'));

    // Валидация языков программирования
    if (!is_array($_POST['lang'])) {
        $_POST['lang'] = [];
    }
    foreach ($_POST['lang'] as $k => $v) {
        if (intval($v) < 1 || intval($v) > 11) {
            unset($_POST['lang'][$k]);
        }
    }
    if (empty($_POST['lang'])) {
        setcookie('lang_error', 'Выберите хотя бы один язык программирования.');
        setcookie('lang_value', '', strtotime('-1 day'));
        $error = true;
    } else {
        setcookie('lang_error', '', strtotime("-1 day"));
        setcookie('lang_value', implode('|', $_POST['lang']), strtotime('+1 year'));
    }

    // Валидация пола
    if (!in_array($_POST['gender'], ['0', '1'])) {
        setcookie('sex_error', 'Выберите корректный пол.');
        $error = true;
    } else {
        setcookie('sex_error', '', strtotime("-1 day"));
    }
    setcookie('sex_value', $_POST['gender'], strtotime('+1 year'));

    // Валидация биографии
    if (strlen($_POST['biography']) > 200) {
        setcookie('bio_error', 'Биография слишком длинная. Уменьшите её.');
        $error = true;
    } else {
        setcookie('bio_error', '', strtotime("-1 day"));
    }
    setcookie('bio_value', $_POST['biography'], strtotime('+1 year'));

    // Если есть ошибки, перенаправляем обратно на форму
    if ($error) {
        header('Location: form.php' . (isset($_GET['numer']) ? '?numer=' . $_GET['numer'] : ''));
        exit();
    }

    include __DIR__ . '/../../../pass.php';

    try {
        if (isset($_SESSION['numer'])) {
            // Обновление существующей заявки
            $stmt = $db->prepare("UPDATE user_applications SET full_name=?, phone_number=?, email_address=?, birth_date=?, gender=?, biography=? WHERE application_id=?");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['phone_number'],
                $_POST['email_address'],
                $_POST['birth_date'],
                $_POST['gender'],
                $_POST['biography'],
                $_SESSION['numer']
            ]);
            $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id=?");
            $stmt->execute([$_SESSION['numer']]);
            foreach ($_POST['lang'] as $value) {
                $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                $stmt->execute([$_SESSION['numer'], $value]);
            }
            unset($_SESSION['numer']);
        } else {
            // Создание новой заявки
            $stmt = $db->prepare("INSERT INTO user_applications (full_name, phone_number, email_address, birth_date, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['phone_number'],
                $_POST['email_address'],
                $_POST['birth_date'],
                $_POST['gender'],
                $_POST['biography']
            ]);
            $id_app = $db->lastInsertId();
            foreach ($_POST['lang'] as $value) {
                $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                $stmt->execute([$id_app, $value]);
            }
            $stmt = $db->prepare("INSERT INTO application_users (application_id, user_login) VALUES (?, ?)");
            $stmt->execute([$id_app, $_SESSION['login']]);
        }
    } catch (PDOException $e) {
        flash('Ошибка базы данных: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        header('Location: index.php');
        exit();
    }

    flash('Данные успешно сохранены.');
    header('Location: index.php');
    exit();
}
?>