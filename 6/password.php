<?php
$password = '123';
$hash = '$2y$10$eImiTXuWVxfM37uY4JANjQe5Jxq2p5s8F5l5a5l5a5l5a5l5a5l5a';

if (password_verify($password, $hash)) {
    echo "Пароль верный!";
} else {
    echo "Пароль неверный!";
}
?>