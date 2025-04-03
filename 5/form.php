<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Форма</title>
    <style>
        .error { color: red; }
    </style>
</head>
<body>

<?php if (!empty($messages)): ?>
    <div id="messages">
        <?php foreach ($messages as $message): ?>
            <p><?= $message ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="" method="post">
    <label for="FIO">ФИО:</label>
    <input type="text" name="FIO" id="FIO" value="<?= isset($_POST['FIO']) ? htmlspecialchars($_POST['FIO']) : '' ?>">
    <?php if (!empty($errors['FIO'])): ?>
        <span class="error"><?= $errors['FIO'] ?></span>
    <?php endif; ?>
    <br><br>

    <label for="tel">Телефон:</label>
    <input type="text" name="tel" id="tel" value="<?= isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '' ?>">
    <?php if (!empty($errors['tel'])): ?>
        <span class="error"><?= $errors['tel'] ?></span>
    <?php endif; ?>
    <br><br>

    <label for="email">Email:</label>
    <input type="text" name="email" id="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
    <?php if (!empty($errors['email'])): ?>
        <span class="error"><?= $errors['email'] ?></span>
    <?php endif; ?>
    <br><br>

    <label for="DR">Дата рождения:</label>
    <input type="date" name="DR" id="DR" value="<?= isset($_POST['DR']) ? htmlspecialchars($_POST['DR']) : '' ?>">
    <br><br>

    <label>Пол:</label>
    <input type="radio" name="sex" value="1" <?= isset($_POST['sex']) && $_POST['sex'] == 1 ? 'checked' : '' ?>> Мужской
    <input type="radio" name="sex" value="0" <?= isset($_POST['sex']) && $_POST['sex'] == 0 ? 'checked' : '' ?>> Женский
    <br><br>

    <label for="bio">Биография:</label>
    <textarea name="bio" id="bio"><?= isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : '' ?></textarea>
    <br><br>

    <label>Языки программирования:</label><br>
    <?php
    $stmt = $pdo->query("SELECT * FROM languages");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($languages as $lang): ?>
        <input type="checkbox" name="languages[]" value="<?= $lang['id_lang'] ?>" 
               <?= isset($_POST['languages']) && in_array($lang['id_lang'], $_POST['languages']) ? 'checked' : '' ?>>
        <?= $lang['lang'] ?><br>
    <?php endforeach; ?>
    <br>

    <input type="submit" value="Отправить">
</form>

<?php if (isset($_SESSION['user_id'])): ?>
    <p><a href="logout.php">Выход</a></p>
<?php else: ?>
    <p><a href="login.php">Войти</a></p>
<?php endif; ?>

</body>
</html>