<?php
require_once __DIR__.'/session.php';
header('Content-Type: text/html; charset=UTF-8');
$errors = array();
function setErrors($name) {
    global $errors;
    $errors[$name] = empty($_COOKIE[$name.'_error']) ? '' : $_COOKIE[$name.'_error'];
}
foreach (array('fio', 'tel', 'email', 'dr', 'sex', 'bio') as $v) {
    setErrors($v);
}
setErrors('lang');
unset($_SESSION['numer']);
if (isset($_GET['numer'])){
    require_once __DIR__.'../../../pass.php';
    $stmt=$db->prepare("SELECT id_app FROM app_users WHERE id_user=? AND id_app=?");
    $stmt->execute([$_SESSION['login'],$_GET['numer']]);
    $apps=$stmt->fetch(PDO::FETCH_NUM);
    if (empty($apps)){
        print('<h2 style="text-align: center;">
            У вас нет права редактировать эту таблицу, у нас на сервере за такое палками бьют!
            </h2>');
        exit();
    }
    $_SESSION['numer']=$_GET['numer'];
    if (empty(array_diff($errors, array('')))){
        $stmt=$db->prepare("SELECT * FROM applications WHERE id_app=?");
        $stmt->execute([$apps[0]]);
        $data=$stmt->fetch(PDO::FETCH_NUM);
        foreach(array('fio', 'tel', 'email', 'dr', 'sex') as $k=>$v){
            $_COOKIE[$v.'_value']=$data[$k+1];
        }
        $_COOKIE['bio_value']=$data[6];
        $stmt=$db->prepare("SELECT id_lang FROM app_langs WHERE id_app=?");
        $stmt->execute([$apps[0]]);
        $data=$stmt->fetchAll(PDO::FETCH_NUM);
        $langs=array();
        foreach($data as $v){
            $langs[]=$v[0];
        }
        $_COOKIE['lang_value']=implode('|', $langs);
    }
}
$values = array();
$languages=empty($_COOKIE['lang_value'])?array():explode("|", $_COOKIE['lang_value']);

function setValue($name) {
    global $values;
    $values[$name] = empty($_COOKIE[$name.'_value']) ? '' : strip_tags($_COOKIE[$name.'_value']);
}

function checkLang($num){
    global $languages;
    print(in_array($num, $languages) ? 'selected' : '');
}

foreach (array('fio', 'tel', 'email', 'dr', 'sex', 'bio') as $v) {
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
        .maxw960 {
            max-width: 960px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php flash();?>
    <form action="./writer.php" method="post" class="px-2 maxw960">
        <label class="form-control bg-<?php print($errors['fio']?'danger':'warning')?> border-0 form-label">
            <?php print($errors['fio']?$errors['fio']:'Введите ФИО:')?>
            <input placeholder="Иванов Иван Иванович" name="FIO" required
            class="form-control req" value="<?php print($values['fio'])?>">
        </label>
        <label class="form-control bg-<?php print($errors['tel']?'danger':'warning')?> border-0 form-label">
            <?php print($errors['tel']?$errors['tel']:'Введите номер телефона:')?>
            <div class="input-group">
                <span class="input-group-text">+7</span>
                <input type="tel" placeholder="(XXX) XXX-XX-XX" name="tel" required 
                class="form-control req" value="<?php print($values['tel'])?>">
            </div>
        </label>
        <label class="form-control bg-<?php print($errors['email']?'danger':'warning')?> border-0 form-label">            
            <?php print($errors['email']?$errors['email']:'Введите email:')?>
            <input type="email" placeholder="email@email.com" name="email" required 
            class="form-control req" value="<?php print($values['email'])?>">
        </label>
        <label class="form-control bg-<?php print($errors['dr']?'danger':'warning')?> border-0 form-label">
            <?php print($errors['dr']?$errors['dr']:'Введите дату рождения:')?>
            <input type="date" name="DR" required 
            class="form-control req" value="<?php print($values['dr'])?>">
        </label>

        <div class="form-control bg-<?php print($errors['sex']?'danger':'warning')?> border-0"> 
            <p class="my-2"><?php print($errors['sex']?$errors['sex']:'Выберите ваш пол:')?></p>            
            <label>
                <input type="radio" name="sex" class="form-check-input" value="0"
                <?php print($values['sex']?'':'checked')?>>
                Мужской
            </label>
            <label>
                <input type="radio" name="sex" class="form-check-input" value="1"
                <?php print($values['sex']?'checked':'')?>>
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

        <label class="form-control bg-<?php print($errors['bio']?'danger':'secondary')?> border-0 my-2 form-label">
            <?php print($errors['bio']?$errors['bio']:'Расскажите о своей жизни (биография):')?>   
            <textarea class="form-control req" name="bio"
            placeholder="Начал писать свои первые произведения﻿ уже в семь лет. Создавал не только стихи﻿, но и произведения в поддержку революционеров — за вольнодумство даже отправляли в ссылки."
            ><?php print($values['bio'])?></textarea>
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