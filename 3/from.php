<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    print("<h1>Вы ошиблись адресом</h1>");
    print("<h2>Форма не на form.php</h2>");
    exit();
}

$errors = FALSE;

$FIO = $_POST['FIO'];
$tel = $_POST['tel'];
$email = $_POST['email'];
$DR = $_POST['DR'];
$sex = $_POST['sex'];
$lang = $_POST['lang']; 
$bio = $_POST['bio'];

if (strlen($FIO) > 150) {
    print('ФИО слишком длинное.<br/>');
    $errors = TRUE;
}

if (preg_match('~[0-9]+~', $FIO)) {
    print('ФИО не должно содержать цифры.<br/>');
    $errors = TRUE;
}

if (!preg_match('~^\+7\d{10}$~', $tel)) {
    print('Номер должен содержать 11 цифр и начинаться с +7.<br/>');
    $errors = TRUE;
}


if (!preg_match('~@~', $email)) {
    print('Email должен содержать \'@\'.<br/>');
    $errors = TRUE;
}

$year = (int)substr($DR, 0, 4);
if ($year < 1800) {
    print('Возраст слишком большой<br/>');
    $errors = TRUE;
} elseif ($year > 2025) {
    print('Некорректная дата<br/>');
    $errors = TRUE;
}

if (empty($lang)) {
    print('Выберите хотя бы один язык программирования.<br/>');
    $errors = TRUE;
}

if (strlen($bio) > 200) {
    print('Биография вмещает максимум 200 символов.<br/>');
    $errors = TRUE;
}

if (empty($bio)) {
    print('Расскажите нам что-нибудь о себе<br/>');
}

if ($errors) {
    exit();
}

include("../../../pass.php");
$db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $stmt = $db->prepare("INSERT INTO applications (FIO, tel, email, DR, sex, bio) VALUES (:FIO, :tel, :email, :DR, :sex, :bio)");
    $stmt->execute([
        'FIO' => $FIO,
        'tel' => $tel,
        'email' => $email,
        'DR' => $DR,
        'sex' => $sex,
        'bio' => $bio
    ]);

    $id_app = $db->lastInsertId();

    foreach ($lang as $id_lang) {
        $stmt = $db->prepare("INSERT INTO app_langs (id_app, id_lang) VALUES (?, ?)");
        $stmt->execute([$id_app, $id_lang]);
    }

    print("Успешно<br/>");

} catch (PDOException $e) {
    print('Error: ' . $e->getMessage());
    exit();
}
?>
