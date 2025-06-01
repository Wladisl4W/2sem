<?php
session_start();
include("../db.php");

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['login']) || empty($_POST['password'])) {
        $error = 'Пожалуйста, заполните оба поля.';
    } else {
        try {
            $db = getDatabaseConnection();

            $stmt = $db->prepare("SELECT user_id, password_hash FROM users WHERE user_login = ?");
            $stmt->execute([$_POST['login']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($_POST['password'], $user['password_hash'])) {
                $_SESSION['user_id'] = intval($user['user_id']);
                header('Location: back/edit.php'); // Редирект при успешном входе
                exit();
            } else {
                $error = 'Неверный логин или пароль.';
            }
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $error = 'Ошибка базы данных. Пожалуйста, попробуйте позже.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="index_style.css">
    <link rel="stylesheet" type="text/css" href="./includes/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="./includes/slick/slick-theme.css"/>
    <script defer src="./includes/jquery-3.4.1.min.js"></script>
    <script defer type="text/javascript" src="./includes/slick/slick.min.js"></script>
    <script defer src="script.js"></script>
    <script defer src="formscript.js"></script>
    <title>Поддержка сайтов на костылях</title>
    <link rel="icon" type="image/x-icon" href="images/tank1.png">
    <audio id="sound"><source src="sounds/probitiye.mp3" type="audio/mp3"></audio>
</head>
<body>
    <video id="headerBackgroundVideo" loop="loop" autoplay="autoplay" preload="auto" muted>
        <source src="./images/header_background.mp4"></source>
        <source src="./images/header_background.webm" type="video/webm"></source>
    </video>
    <img id="headerBackgroundImage" src="./images/header_kostyl.png">
    
    <header>
        <div id="title">
            <span>
                <a href="./index.html">
                    Поддержка сайтов<br>на костылях
                </a>
            </span>
        </div>
        
        <nav>
            <div class="nav-item dropdown">
                контакты
                <div class="dropdown-wrapper">
                    <div class="dropdown-item">
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">внеконтакта</a>
                    </div>
                    <div class="dropdown-item">
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">телетонна</a>
                    </div>
                    <div class="dropdown-item">
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">гитангар</a>
                    </div>
                    <div class="dropdown-item">
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">крутой текст</a>
                    </div>
                </div>
            </div>
            <div class="nav-item">
                <a href="./about_us.html">
                    <span>о нас</span>
                </a>
            </div>
            <div class="nav-item" style="cursor: wait;">
                <span>крутой текст</span>
            </div>
        </nav>
    </header>

    <div id="menuMobile">
        <ul>
            <li id="menuMobileButton">
                <p>меню</p>
            </li>
            <li style="border-top: 2px solid #ffffff; display: none;" class="menuMobileItem ">
                <p>контакты</p>
                <ul style="padding: 0 2vw;">
                    <li>
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">внеконтакта</a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">телетонна</a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo">гитангар</a>
                    </li>
                </ul>
            </li>
            <li style="border-top: 2px solid #ffffff; display: none;" class="menuMobileItem">
                <p>
                    <a href="./about_us.html">о нас</a>
                </p>
            </li>
            <li style="border-top: 2px solid #ffffff; display: none;" class="menuMobileItem">
                <p>крутой текст</p>
            </li>
        </ul>
    </div>

    <main>
        <button type="button" class="btn formOpenButton" id="showFormBtn" data-bs-toggle="modal" data-bs-target="#modalForm"></button>
        <div class="modal modal-lg" id="modalForm" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header justify-content-center form-head" style="background-color: #131313;">
                        <h1 class="text-white">Вход</h1>
                    </div>
                    <div class="modal-body" style="background-color: #131313;">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form id="loginForm" method="post" action="" class="p-4">
                            <div class="mb-3">
                                <label for="login" class="form-label text-white">Логин:</label>
                                <input type="text" id="login" name="login" class="form-control" placeholder="Введите ваш логин" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label text-white">Пароль:</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Введите ваш пароль" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary w-45">Войти</button>
                                <a href="back/form.php" class="btn btn-secondary w-45">Регистрация</a>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer" style="background-color: #131313;">
                        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="gallery">
            <div class="galleryPhoto">
                <img src="images/tank1.png" alt="tank1">
            </div>    
            <div class="galleryPhoto">
                <img src="images/tank2.png" alt="tank2">
            </div>    
            <div class="galleryPhoto">
                <img src="images/tank3.png" alt="tank3">
            </div>    
            <div class="galleryPhoto">
                <img src="images/tank4.png" alt="tank4">
            </div>    
            <div class="galleryPhoto">
                <img src="images/tank5.png" alt="tank5">
            </div>    
        </div>    

        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"> 
            <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false"> 
                <div class="toast-header"> 
                    <strong class="me-auto">Анна</strong> 
                    <small>1 метр от вас</small> 
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button> 
                </div> 
                <div class="toast-body"> 
                    <img src="images/vladolga.jpg" class="rounded me-2 w-100" alt="..."> 
                </div> 
            </div> 
        </div>
    </main>

    <velikiyKostil style="flex: 1"></velikiyKostil>
    <footer>
        <div id="footerContent">   
            <div class="footerText">
                <p style="margin: 0;">О нас?</p>
            </div>

            <div class="footerButtons">
                <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo" target="_blank">
                    <img src="images/footer_logoTG.png" alt="TG" class="footerButtonImage">
                </a>
                <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo" target="_blank">
                    <img src="images/footer_logoVK.png" alt="VK" class="footerButtonImage">
                </a>
                <a href="https://www.youtube.com/watch?v=nGBYEUNKPmo" target="_blank">
                    <img src="images/footer_logoYT.png" alt="YT" class="footerButtonImage">
                </a>
            </div>
        </div>
        <div style="position: absolute; z-index: -1;">
            <img src="images/footer_background.png" alt="ryaniGoslingi" width="100%">
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('loginForm');
            const loginModal = new bootstrap.Modal(document.getElementById('modalForm'));

            // Если есть ошибка, открываем модальное окно
            <?php if (!empty($error)): ?>
            loginModal.show();
            <?php endif; ?>

            // Предотвращаем закрытие модального окна при отправке формы
            loginForm.addEventListener('submit', (event) => {
                if (!loginForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                loginForm.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>