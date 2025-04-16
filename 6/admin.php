<?php
session_start();
include("../db.php");

$db = getDatabaseConnection();

// HTTP-аутентификация
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

$stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE admin_login = ?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || md5($_SERVER['PHP_AUTH_PW']) != $admin['password_hash']) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

// Удаление данных пользователя
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM user_applications WHERE application_id = ?");
    $stmt->execute([$_POST['delete_id']]);
    header('Location: admin.php');
    exit();
}

// Получение всех данных пользователей
$stmt = $db->query("SELECT ua.application_id, ua.full_name, ua.phone_number, ua.email_address, ua.birth_date, ua.gender, ua.biography, GROUP_CONCAT(pl.language_name SEPARATOR ', ') AS languages
                    FROM user_applications ua
                    LEFT JOIN application_languages al ON ua.application_id = al.application_id
                    LEFT JOIN programming_languages pl ON al.language_id = pl.language_id
                    GROUP BY ua.application_id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение статистики по языкам программирования
$stmt = $db->query("SELECT pl.language_name, COUNT(al.application_id) AS user_count
                    FROM programming_languages pl
                    LEFT JOIN application_languages al ON pl.language_id = al.language_id
                    GROUP BY pl.language_id");
$languageStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .centered {
            text-align: center;
        }
        .table-container {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-wide">
        <div class="header-box">Админ-панель</div>

        <h2 class="centered">Данные пользователей</h2>
        <div class="table-container">
            <table class="table table-dark table-striped w-75">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Дата рождения</th>
                        <th>Пол</th>
                        <th>Биография</th>
                        <th>Языки</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['application_id']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td><?= htmlspecialchars($user['email_address']) ?></td>
                            <td><?= htmlspecialchars($user['birth_date']) ?></td>
                            <td><?= $user['gender'] == 0 ? 'Мужской' : 'Женский' ?></td>
                            <td><?= htmlspecialchars($user['biography']) ?></td>
                            <td><?= htmlspecialchars($user['languages']) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $user['application_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                </form>
                                <a href="edit.php?application_id=<?= $user['application_id'] ?>" class="btn btn-warning btn-sm">Редактировать</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="centered">Статистика по языкам программирования</h2>
        <div class="table-container">
            <table class="table table-dark table-striped w-50">
                <thead>
                    <tr>
                        <th>Язык</th>
                        <th>Количество пользователей</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($languageStats as $stat): ?>
                        <tr>
                            <td><?= htmlspecialchars($stat['language_name']) ?></td>
                            <td><?= htmlspecialchars($stat['user_count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
