<?php
require_once __DIR__.'/session.php';
if (signedin()){
	header('Location: index.php');
	exit();
}
function randomPassword($length=12): string {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = '';
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass.= $alphabet[$n];
    }
    return $pass;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (!preg_match('/^user[0-9]+$/', $_POST['login'])){
        flash('Неверный логин или пароль (хи-хи)');
    }
    else { 
        include __DIR__.'/../../../pass.php';
        $stmt=$db->prepare("SELECT id_user, pass FROM users WHERE login=?");
        $stmt->execute([$_POST['login']]);
        $row=$stmt->fetch(PDO::FETCH_NUM);
        if (password_verify($_POST['pass'], $row[1])){
            session_start();
            $_SESSION['active']=time();
            $_SESSION['login']=$row[0];
            $_SESSION['signin']=true;
            header('Location: index.php');
	        exit();
        } 
        else {
            flash('Неверный логин или пароль (хи-хи)');
        }
    }
}

if(array_key_exists('register', $_GET)){  
    if (registered()){
        flash('Вы уже зарегестрированы');
    } 
    else {
        include __DIR__.'/../../../pass.php';
        $stmt=$db->prepare("SELECT MAX(id_user) FROM users");
        $stmt->execute();
        $login=$stmt->fetch(PDO::FETCH_NUM)[0];
        $login='user'.(($login+3)*129-89+pow($login+7,2));
        $password=randomPassword();
        $hash=password_hash($password, PASSWORD_DEFAULT);
        flash('Ваш логин: '.$login.'<br>Ваш пароль: '.$password);
        $stmt=$db->prepare("INSERT INTO users VALUES(0,?,?)");
        $stmt->execute([$login,$hash]);
        session_start();
    }
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
            width: 50vw;
        }
    </style>
</head>
<body>
    <?php flash();?>
    <form action="./login.php" method="post" class="px-2 maxw960 position-absolute top-50 translate-middle start-50">
        <label class="form-control bg-warning border-0 form-label">
            Введите логин:
            <input name="login" required class="form-control req">
        </label>
        <label class="form-control bg-warning border-0 form-label">
            Введите пароль:
            <input type="password" name="pass" required class="form-control req">
        </label>
        <div class="form-control d-flex">
            <a href="?register=true" class="btn-secondary btn m-1 w-50">Зарегестрироваться</a>
            <input type="submit" value="Войти" class="btn-success btn m-1 w-50">
        </div>
    </form>
</body>
</html>