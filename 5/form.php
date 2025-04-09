<?php
if (!isset($messages)) {
    $messages = [];
}
if (!isset($errors)) {
    $errors = [];
}
if (!isset($values)) {
    $values = [
        'fio' => '',
        'tel' => '',
        'email' => '',
        'dr' => '',
        'sex' => 1,
        'bio' => '',
        'languages' => []
    ];
}

if (!empty($messages)) {
    print('<div class="messages">');
    foreach ($messages as $message) {
        print($message);
    }
    print('</div>');
}
?>

<form action="" method="POST" class="container">
    <h1>Регистрация</h1>
    <label for="fio">ФИО:</label>
    <input type="text" name="fio" id="fio" <?php if ($errors['fio'] ?? false) {print 'class="error"';} ?> value="<?php print htmlspecialchars($values['fio'] ?? ''); ?>" />

    <label for="tel">Телефон:</label>
    <input type="text" name="tel" id="tel" <?php if ($errors['tel'] ?? false) {print 'class="error"';} ?> value="<?php print htmlspecialchars($values['tel'] ?? ''); ?>" />

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" <?php if ($errors['email'] ?? false) {print 'class="error"';} ?> value="<?php print htmlspecialchars($values['email'] ?? ''); ?>" />

    <label for="dr">Дата рождения:</label>
    <input type="date" name="dr" id="dr" <?php if ($errors['dr'] ?? false) {print 'class="error"';} ?> value="<?php print htmlspecialchars($values['dr'] ?? ''); ?>" />

    <label for="sex">Пол:</label>
    <select name="sex" id="sex" <?php if ($errors['sex'] ?? false) {print 'class="error"';} ?>>
        <option value="1" <?php if (($values['sex'] ?? 1) == 1) print 'selected'; ?>>Мужской</option>
        <option value="2" <?php if (($values['sex'] ?? 1) == 2) print 'selected'; ?>>Женский</option>
    </select>

    <label for="bio">Биография:</label>
    <textarea name="bio" id="bio" <?php if ($errors['bio'] ?? false) {print 'class="error"';} ?>><?php print htmlspecialchars($values['bio'] ?? ''); ?></textarea>

    <label for="languages">Языки программирования:</label>
    <select name="languages[]" id="languages" multiple>
        <?php
        if (isset($db)) {
            $stmt = $db->query("SELECT * FROM languages");
            while ($lang = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = in_array($lang['id_lang'], ($values['languages'] ?? [])) ? 'selected' : '';
                echo "<option value='{$lang['id_lang']}' {$selected}>{$lang['lang']}</option>";
            }
        }
        ?>
    </select>

    <input type="submit" value="Отправить" />
</form>