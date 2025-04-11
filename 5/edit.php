<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include("../../../pass.php");

try {
    $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $db->prepare("UPDATE user_applications SET full_name = ?, phone_number = ?, email_address = ?, birth_date = ?, gender = ?, biography = ? WHERE application_id = (SELECT application_id FROM users WHERE user_id = ?)");
        $stmt->execute([
            $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $_SESSION['user_id']
        ]);
        $success = 'Данные успешно обновлены.';
    }

    $stmt = $db->prepare("SELECT full_name, phone_number, email_address, birth_date, gender, biography FROM user_applications WHERE application_id = (SELECT application_id FROM users WHERE user_id = ?)");
    $stmt->execute([$_SESSION['user_id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Ошибка БД: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование данных</title>
</head>
<body>
    <h1>Редактирование данных</h1>
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>ФИО: <input type="text" name="FIO" value="<?= htmlspecialchars($data['full_name']) ?>" required></label><br>
        <label>Телефон: <input type="tel" name="tel" value="<?= htmlspecialchars($data['phone_number']) ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($data['email_address']) ?>" required></label><br>
        <label>Дата рождения: <input type="date" name="DR" value="<?= htmlspecialchars($data['birth_date']) ?>" required></label><br>
        <label>Пол: 
            <select name="sex" required>
                <option value="0" <?= $data['gender'] == '0' ? 'selected' : '' ?>>Мужской</option>
                <option value="1" <?= $data['gender'] == '1' ? 'selected' : '' ?>>Женский</option>
            </select>
        </label><br>
        <label>Биография: <textarea name="bio" required><?= htmlspecialchars($data['biography']) ?></textarea></label><br>
        <button type="submit">Сохранить</button>
    </form>
</body>
</html>
