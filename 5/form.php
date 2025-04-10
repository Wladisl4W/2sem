<?php
require_once __DIR__.'/session.php';
header('Content-Type: text/html; charset=UTF-8');
$errors = array();

function setErrors($name) {
    global $errors;
    $errors[$name] = empty($_COOKIE[$name.'_error']) ? '' : htmlspecialchars($_COOKIE[$name.'_error'], ENT_QUOTES, 'UTF-8');
}

foreach (array('full_name', 'phone_number', 'email_address', 'birth_date', 'gender', 'biography') as $v) {
    setErrors($v);
}
setErrors('lang');
unset($_SESSION['numer']);

if (isset($_GET['numer'])){
    require_once __DIR__.'/../../../pass.php';
    $stmt = $db->prepare("SELECT application_id FROM application_users WHERE user_login=? AND application_id=?");
    $stmt->execute([$_SESSION['login'], $_GET['numer']]);
    $apps = $stmt->fetch(PDO::FETCH_NUM);
    if (empty($apps)){
        print('<h2 style="text-align: center; color: red;">
            У вас нет прав для редактирования данной записи.
            </h2>');
        exit();
    }
    $_SESSION['numer'] = $_GET['numer'];
    if (empty(array_diff($errors, array('')))){
        $stmt = $db->prepare("SELECT * FROM user_applications WHERE application_id=?");
        $stmt->execute([$apps[0]]);
        $data = $stmt->fetch(PDO::FETCH_NUM);
        foreach(array('full_name', 'phone_number', 'email_address', 'birth_date', 'gender') as $k => $v){
            $_COOKIE[$v.'_value'] = htmlspecialchars($data[$k+1], ENT_QUOTES, 'UTF-8');
        }
        $_COOKIE['biography_value'] = htmlspecialchars($data[6], ENT_QUOTES, 'UTF-8');
        $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id=?");
        $stmt->execute([$apps[0]]);
        $data = $stmt->fetchAll(PDO::FETCH_NUM);
        $langs = array();
        foreach($data as $v){
            $langs[] = $v[0];
        }
        $_COOKIE['lang_value'] = implode('|', $langs);
    }
}

$values = array();
$languages = empty($_COOKIE['lang_value']) ? array() : explode("|", $_COOKIE['lang_value']);

function setValue($name) {
    global $values;
    $values[$name] = empty($_COOKIE[$name.'_value']) ? '' : htmlspecialchars($_COOKIE[$name.'_value'], ENT_QUOTES, 'UTF-8');
}

function checkLang($num){
    global $languages;
    print(in_array($num, $languages) ? 'selected' : '');
}

foreach (array('full_name', 'phone_number', 'email_address', 'birth_date', 'gender', 'biography') as $v) {
    setValue($v);
}
?>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР5</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #f5f5f5;
        }
        .form-control {
            background-color: #1e1e1e;
            color: #f5f5f5;
        }
        .form-control:focus {
            border-color: #ff69b4;
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.25);
        }
        .btn-success {
            background-color: #ff69b4;
            border-color: #ff69b4;
        }
        .btn-success:hover {
            background-color: #ff85c1;
            border-color: #ff85c1;
        }
        .btn-secondary {
            background-color: #333;
            border-color: #333;
        }
        .btn-secondary:hover {
            background-color: #444;
            border-color: #444;
        }
        .maxw960 {
            max-width: 960px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php flash();?>
    <form action="./writer.php" method="post" class="px-2 maxw960">
        <label class="form-control bg-<?php print($errors['full_name']?'danger':'warning')?> border-0 form-label">
            <?php print($errors['full_name']?$errors['full_name']:'Введите ФИО:')?>
            <input placeholder="Иванов Иван Иванович" name="full_name" required
            class="form-control req" value="<?php print($values['full_name'])?>">
        </label>
        <label class="form-control bg-<?php print($errors['phone_number']?'danger':'warning')?> border-0 form-label">
            <?php print($errors['phone_number']?$errors['phone_number']:'Введите номер телефона:')?>
            <div class="input-group">
                <span class="input-group-text">+7</span>
                <input type="tel" placeholder="(XXX) XXX-XX-XX" name="phone_number" required 
                class="form-control req" value="<?php print($values['phone_number'])?>">
            </div>
        </label>
        <label class="form-control bg-<?php print($errors['email_address']?'danger':'warning')?> border-0 form-label">            
            <?php print($errors['email_address']?$errors['email_address']:'Введите email:')?>
            <input type="email" placeholder="email@email.com" name="email_address" required 
            class="form-control req" value="<?php print($values['email_address'])?>">
        </label>
        <label class="form-control bg-<?php print($errors['birth_date']?'danger':'warning')?> border-0 form-label">
            <?php print($errors['birth_date']?$errors['birth_date']:'Введите дату рождения:')?>
            <input type="date" name="birth_date" required 
            class="form-control req" value="<?php print($values['birth_date'])?>">
        </label>

        <div class="form-control bg-<?php print($errors['gender']?'danger':'warning')?> border-0"> 
            <p class="my-2"><?php print($errors['gender']?$errors['gender']:'Выберите ваш пол:')?></p>            
            <label>
                <input type="radio" name="gender" class="form-check-input" value="0"
                <?php print($values['gender']?'':'checked')?>>
                Мужской
            </label>
            <label>
                <input type="radio" name="gender" class="form-check-input" value="1"
                <?php print($values['gender']?'checked':'')?>>
                Женский
            </label>
        </div>

        <label class="form-control bg-<?php print($errors['lang']?'danger':'warning')?> border-0 form-label my-2">
            <?php print($errors['lang']?$errors['lang']:'Выберите любимый(-ые) язык(-и) программирования:')?>            
            <select multiple class="form-select req" name="lang[]">
                <option <?php checkLang(1)?> value="1">Pascal</option>
                <option <?php checkLang(2)?> value="2">C</option>
                <option <?php checkLang(3)?> value="3">C++</option>
                <option <?php checkLang(4)?> value="4">JavaScript</option>
                <option <?php checkLang(5)?> value="5">PHP</option>
                <option <?php checkLang(6)?> value="6">Python</option>
                <option <?php checkLang(7)?> value="7">Java</option>
                <option <?php checkLang(8)?> value="8">Haskel</option>
                <option <?php checkLang(9)?> value="9">Clojure</option>
                <option <?php checkLang(10)?> value="10">Prolog</option>
                <option <?php checkLang(11)?> value="11">Scala</option>
            </select>
        </label>

        <label class="form-control bg-<?php print($errors['biography']?'danger':'secondary')?> border-0 my-2 form-label">
            <?php print($errors['biography']?$errors['biography']:'Расскажите о своей жизни (биография):')?>   
            <textarea class="form-control req" name="biography"
            placeholder="Начал писать свои первые произведения﻿ уже в семь лет. Создавал не только стихи﻿, но и произведения в поддержку революционеров — за вольнодумство даже отправляли в ссылки."
            ><?php print($values['biography'])?></textarea>
        </label>

        <label class="form-control bg-warning border-0 my-2 form-label">
            <input type="checkbox" class="form-check-input" required>
            С контрактом ознакомлен(-а)
        </label>
        <div class="form-control d-flex">
            <a href="." class="btn-secondary btn m-2 w-50">Отмена</a>
            <input type="submit" value="Сохранить" class="btn-success btn m-2 w-50 form-control">
        </div>
    </form>
</body>
</html>