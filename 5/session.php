<?php
function registered(): bool{
    return isset($_COOKIE[session_name()]);
}
function signedin(): bool{
    return isset($_SESSION['signin']);
}
function flash(?string $message = null){
    if ($message) {
        setcookie('flash',$message);
        $_COOKIE['flash']=$message;
    }
    else {
        if (!empty($_COOKIE['flash'])) {
            print('
            <p class="text-center bg-primary mb-4">
                '.$_COOKIE['flash'].'
            </p>');
         }
        setcookie('flash', '', strtotime("-1 day"));
        unset($_COOKIE['flash']);
    }
}
if (str_contains($_SERVER['SCRIPT_NAME'], 'login.php')){
    if (registered()){
        session_start();
        if (isset($_SESSION['signin'])){
	        header('Location: index.php');
	        exit();
        }
    }
}
else {
    if (!registered()){
         header('Location: login.php');
         exit();
    }
    session_start();
    if (isset($_SESSION['active']) and (time() - $_SESSION['active'] > 3600*24)) {
        unset($_SESSION['signin']);
    }
    if (!signedin()){
        header('Location: login.php');
        exit();
    }
}
?>