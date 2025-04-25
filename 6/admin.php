<?php
session_start();
include("../db.php");

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic realm="Админ-панель"');
    header('HTTP/1.0 401 Unauthorized');
    die('Доступ запрещён');
}

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE admin_login = ?");
    $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !password_verify($_SERVER['PHP_AUTH_PW'], $admin['password_hash'])) {
        header('HTTP/1.0 403 Forbidden');
        die('Доступ запрещён');
    }
} catch (PDOException $e) {
    die('Ошибка базы данных: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM user_applications WHERE application_id = ?");
        $stmt->execute([$_POST['delete_id']]);
        header('Location: admin.php');
        exit();
    } catch (PDOException $e) {
        $error = 'Ошибка при удалении пользователя: ' . $e->getMessage();
    }
}

try {
    $stmt = $db->query("SELECT ua.application_id, ua.full_name, ua.phone_number, ua.email_address, ua.birth_date, ua.gender, ua.biography, GROUP_CONCAT(pl.language_name SEPARATOR ', ') AS languages
                        FROM user_applications ua
                        LEFT JOIN application_languages al ON ua.application_id = al.application_id
                        LEFT JOIN programming_languages pl ON al.language_id = pl.language_id
                        GROUP BY ua.application_id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->query("SELECT pl.language_name, COUNT(al.language_id) AS user_count
                        FROM programming_languages pl
                        LEFT JOIN application_languages al ON pl.language_id = al.language_id
                        GROUP BY pl.language_id");
    $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Ошибка базы данных: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-wide">
        <div class="header-box">Админ-панель</div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <h2 class="text-center my-4">Статистика</h2>
        <table class="table table-dark table-striped w-100">
            <thead>
                <tr>
                    <th>Язык программирования</th>
                    <th>Количество пользователей</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics as $stat): ?>
                    <tr>
                        <td><?= htmlspecialchars($stat['language_name']) ?></td>
                        <td><?= htmlspecialchars($stat['user_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="text-center my-4">Пользователи</h2>
        <div class="table-responsive" style="width: 95%; margin: 0 auto;">
            <table class="table table-dark table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Дата рождения</th>
                        <th>Пол</th>
                        <th>Биография</th>
                        <th>Языки программирования</th>
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
                                <a href="edit.php?id=<?= $user['application_id'] ?>" class="btn btn-sm btn-primary">Редактировать</a>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $user['application_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
