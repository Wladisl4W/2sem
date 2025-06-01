<?php
session_start();
if (empty($_SESSION['login']) || empty($_SESSION['password'])) {
    header('Location: ../index.html'); // Обновляем путь к index.html
    exit();
}

$login = $_SESSION['login'];
$password = $_SESSION['password'];

// Удаляем пароль из сессии сразу после его извлечения
unset($_SESSION['password']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Успешная регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../index_style.css"> <!-- Используем стили сайта -->
    <link rel="icon" type="image/x-icon" href="../images/tank1.png"> <!-- Иконка сайта -->
</head>
<body>
    <header>
        <div id="title">
            <span>
                <a href="../index.html">
                    Поддержка сайтов<br>на костылях
                </a>
            </span>
        </div>
    </header>

    <main class="container-wide">
        <div class="header-box">Регистрация успешна!</div>
        <div class="card mx-auto" style="max-width: 600px; background-color: #131313; color: #fff; border: none;">
            <div class="card-body text-center">
                <h2 class="mb-4">Ваши данные</h2>
                <p><strong>Логин:</strong></p>
                <p class="highlight"><?= htmlspecialchars($login) ?></p>
                <p><strong>Пароль:</strong></p>
                <p class="highlight"><?= htmlspecialchars($password) ?></p>
                <a href="../index.html" class="btn btn-primary w-100 mt-4">На главную</a>
            </div>
        </div>
    </main>

    <footer>
        <div id="footerContent">   
            <div class="footerText">
                <p style="margin: 0;">О нас?</p>
            </div>
            <div class="footerButtons">
                <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo" target="_blank">
                    <img src="../images/footer_logoTG.png" alt="TG" class="footerButtonImage">
                </a>
                <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo" target="_blank">
                    <img src="../images/footer_logoVK.png" alt="VK" class="footerButtonImage">
                </a>
                <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo" target="_blank">
                    <img src="../images/footer_logoYT.png" alt="YT" class="footerButtonImage">
                </a>
            </div>
        </div>
        <div style="position: absolute; z-index: -1;">
            <img src="../images/footer_background.png" alt="ryaniGoslingi" width="100%">
        </div>
    </footer>
</body>
</html>
