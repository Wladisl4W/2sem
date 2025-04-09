<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

require_once 'db.php'; // Подключение к базе данных

$messages = [];
$errors = [];
$values = [
    'fio' => '',
    'tel' => '',
    'email' => '',
    'dr' => '',
    'sex' => 1,
    'bio' => '',
    'languages' => []
];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_SESSION['login'])) {
        $stmt = $db->prepare("SELECT * FROM applications WHERE login = :login");
        $stmt->execute([':login' => $_SESSION['login']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $values = [
                'fio' => $user['FIO'],
                'tel' => $user['tel'],
                'email' => $user['email'],
                'dr' => $user['DR'],
                'sex' => $user['sex'],
                'bio' => $user['bio']
            ];

            $stmt = $db->prepare("SELECT id_lang FROM app_langs WHERE id_app = :id_app");
            $stmt->execute([':id_app' => $user['id_app']]);
            $values['languages'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }
} else {
    // Валидация данных
    if (empty($_POST['fio'])) {
        $errors['fio'] = true;
    } else {
        $values['fio'] = $_POST['fio'];
    }

    if (empty($_POST['tel'])) {
        $errors['tel'] = true;
    } else {
        $values['tel'] = $_POST['tel'];
    }

    if (empty($_POST['email'])) {
        $errors['email'] = true;
    } else {
        $values['email'] = $_POST['email'];
    }

    if (empty($_POST['dr'])) {
        $errors['dr'] = true;
    } else {
        $values['dr'] = $_POST['dr'];
    }

    $values['sex'] = $_POST['sex'];
    $values['bio'] = $_POST['bio'];
    $values['languages'] = isset($_POST['languages']) ? $_POST['languages'] : [];

    if (empty($errors)) {
        if (empty($_SESSION['login'])) {
            // Генерация логина и пароля
            $fio_parts = explode(' ', $values['fio']);
            $login = strtolower($fio_parts[0][0] . $fio_parts[1]);
            $pass = substr(md5(uniqid()), 0, 8);
            $password_hash = password_hash($pass, PASSWORD_DEFAULT);

            // Сохранение в базу
            $stmt = $db->prepare("INSERT INTO applications (FIO, tel, email, DR, sex, bio, login, password_hash) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $values['fio'],
                $values['tel'],
                $values['email'],
                $values['dr'],
                $values['sex'],
                $values['bio'],
                $login,
                $password_hash
            ]);

            $id_app = $db->lastInsertId();

            foreach ($values['languages'] as $lang_id) {
                $stmt = $db->prepare("INSERT INTO app_langs (id_app, id_lang) VALUES (?, ?)");
                $stmt->execute([$id_app, $lang_id]);
            }

            setcookie('login', $login, time() + 86400);
            setcookie('pass', $pass, time() + 86400);
            $messages[] = "Логин: $login, Пароль: $pass";
        } else {
            // Обновление данных
            $stmt = $db->prepare("UPDATE applications SET FIO = ?, tel = ?, email = ?, DR = ?, sex = ?, bio = ? WHERE login = ?");
            $stmt->execute([
                $values['fio'],
                $values['tel'],
                $values['email'],
                $values['dr'],
                $values['sex'],
                $values['bio'],
                $_SESSION['login']
            ]);

            $stmt = $db->prepare("DELETE FROM app_langs WHERE id_app = ?");
            $stmt->execute([$user['id_app']]);

            foreach ($values['languages'] as $lang_id) {
                $stmt = $db->prepare("INSERT INTO app_langs (id_app, id_lang) VALUES (?, ?)");
                $stmt->execute([$user['id_app'], $lang_id]);
            }

            $messages[] = "Данные успешно обновлены.";
        }
    }
}

include 'form.php';
?>