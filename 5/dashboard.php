<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

include("../../../pass.php");
try {
    $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $db->prepare("SELECT * FROM user_applications WHERE application_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: login_form.php');
        exit();
    }
} catch (PDOException $e) {
    echo 'Ошибка БД: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Личный кабинет</h1>
        <p><strong>ФИО:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
        <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email_address']) ?></p>
        <p><strong>Дата рождения:</strong> <?= htmlspecialchars($user['birth_date']) ?></p>
        <p><strong>Пол:</strong> <?= $user['gender'] == 0 ? 'Мужской' : 'Женский' ?></p>
        <p><strong>Биография:</strong> <?= htmlspecialchars($user['biography']) ?></p>
        <a href="logout.php" class="btn btn-danger">Выйти</a>
    </div>
</body>
</html>
