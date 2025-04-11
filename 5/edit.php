<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include("../../../pass.php");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    // Валидация ФИО
    if (empty($_POST['FIO']) || strlen($_POST['FIO']) > 150) {
        $errors['FIO'] = 'ФИО не должно быть пустым и не должно превышать 150 символов.';
    } elseif (preg_match('/\d/', $_POST['FIO'])) {
        $errors['FIO'] = 'ФИО не должно содержать цифры.';
    }

    // Валидация телефона
    if (empty($_POST['tel']) || !preg_match('/^\+?[0-9]{10,15}$/', $_POST['tel'])) {
        $errors['tel'] = 'Введите корректный номер телефона.';
    }

    // Валидация email
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email.';
    }

    // Валидация даты рождения
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

    // Валидация пола
    if (!isset($_POST['sex']) || !in_array($_POST['sex'], ['0', '1'])) {
        $errors['sex'] = 'Выберите корректный пол.';
    }

    // Валидация биографии
    if (empty($_POST['bio']) || strlen($_POST['bio']) > 1000) {
        $errors['bio'] = 'Биография не должна быть пустой и не должна превышать 1000 символов.';
    }

    // Валидация языков программирования
    if (empty($_POST['lang']) || !is_array($_POST['lang'])) {
        $errors['lang'] = 'Выберите хотя бы один язык программирования.';
    }

    if (empty($errors)) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Обновление данных заявки
            $stmt = $db->prepare("UPDATE user_applications SET full_name = ?, phone_number = ?, email_address = ?, birth_date = ?, gender = ?, biography = ? WHERE application_id = ?");
            $stmt->execute([
                $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $_SESSION['application_id']
            ]);

            // Удаление старых языков
            $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
            $stmt->execute([$_SESSION['application_id']]);

            // Добавление новых языков
            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($_POST['lang'] as $languageId) {
                $stmt->execute([$_SESSION['application_id'], $languageId]);
            }

            $success = 'Данные успешно обновлены.';
        } catch (PDOException $e) {
            die('Ошибка БД: ' . $e->getMessage());
        }
    }
}

try {
    $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Обработка выхода из аккаунта
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // Получение ID заявки текущего пользователя
    $stmt = $db->prepare("SELECT application_id FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $applicationId = $stmt->fetchColumn();

    if (!$applicationId) {
        die('Ошибка: заявка не найдена.');
    }

    $_SESSION['application_id'] = $applicationId;

    // Получение данных заявки
    $stmt = $db->prepare("SELECT full_name, phone_number, email_address, birth_date, gender, biography FROM user_applications WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Получение выбранных языков
    $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $selectedLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Получение всех языков программирования
    $stmt = $db->query("SELECT language_id, language_name FROM programming_languages");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Ошибка БД: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212; /* Чуть светлее фон сайта */
            color: #ffffff;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .container {
            max-width: 600px;
        }
        .form-control, .form-select, .form-check-input {
            background-color: #2a2a2a; /* Чуть светлее ячейки формы */
            color: #ffffff;
            border: 1px solid #444;
        }
        .form-control:focus, .form-select:focus {
            border-color: #e91e63; /* Розовый акцент */
            box-shadow: 0 0 5px #e91e63;
        }
        .btn-custom {
            background-color: #e91e63;
            color: #ffffff;
            border: none;
        }
        .btn-custom:hover {
            background-color: #c2185b;
        }
        .btn-logout {
            background-color: #d9534f;
            color: #ffffff;
            border: none;
        }
        .btn-logout:hover {
            background-color: #c9302c;
        }
        .logout-container {
            margin-top: 20px;
        }
        .alert {
            background-color: #333;
            color: #4caf50;
            border: 1px solid #4caf50;
        }
        .bg-dark {
            background-color: #1e1e1e !important; /* Фон формы без оттенков */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Редактирование данных</h1>
        <?php if (!empty($success)): ?>
            <div class="alert text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert text-center">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="p-4 border rounded bg-dark">
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control" value="<?= htmlspecialchars($data['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control" value="<?= htmlspecialchars($data['phone_number']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email_address']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control" value="<?= htmlspecialchars($data['birth_date']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пол:</label>
                <select name="sex" class="form-select">
                    <option value="0" <?= $data['gender'] == '0' ? 'selected' : '' ?>>Мужской</option>
                    <option value="1" <?= $data['gender'] == '1' ? 'selected' : '' ?>>Женский</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Любимые языки программирования:</label>
                <?php foreach ($languages as $language): ?>
                    <div class="form-check">
                        <input type="checkbox" name="lang[]" value="<?= $language['language_id'] ?>" class="form-check-input"
                            <?= in_array($language['language_id'], $selectedLanguages) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= htmlspecialchars($language['language_name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Биография:</label>
                <textarea name="bio" class="form-control" rows="4" required><?= htmlspecialchars($data['biography']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Сохранить</button>
        </form>
        <div class="logout-container">
            <form method="post">
                <button type="submit" name="logout" class="btn btn-logout w-100">Выйти</button>
            </form>
        </div>
    </div>
</body>
</html>
