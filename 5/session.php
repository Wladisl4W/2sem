<?php
/**
 * Проверяет, зарегистрирован ли пользователь (по наличию cookie сессии).
 */
function registered(): bool {
    return isset($_COOKIE[session_name()]);
}

/**
 * Проверяет, вошёл ли пользователь в систему.
 */
function signedin(): bool {
    return isset($_SESSION['signin']);
}

/**
 * Устанавливает или выводит flash-сообщение.
 */
function flash(?string $message = null) {
    if ($message) {
        // Устанавливаем flash-сообщение
        setcookie('flash', $message);
        $_COOKIE['flash'] = $message;
    } else {
        // Выводим flash-сообщение, если оно есть
        if (!empty($_COOKIE['flash'])) {
            print('
            <div class="alert alert-primary text-center mb-4" role="alert">
                ' . htmlspecialchars($_COOKIE['flash'], ENT_QUOTES, 'UTF-8') . '
            </div>');
        }
        // Удаляем flash-сообщение
        setcookie('flash', '', strtotime("-1 day"));
        unset($_COOKIE['flash']);
    }
}

// Если пользователь находится на странице входа
if (str_contains($_SERVER['SCRIPT_NAME'], 'login.php')) {
    if (registered()) {
        session_start();
        // Если пользователь уже вошёл, перенаправляем на главную страницу
        if (signedin()) {
            header('Location: index.php');
            exit();
        }
    }
} else {
    // Если пользователь не зарегистрирован, перенаправляем на страницу входа
    if (!registered()) {
        header('Location: login.php');
        exit();
    }

    // Стартуем сессию
    session_start();

    // Проверяем, не истекло ли время активности пользователя
    if (isset($_SESSION['active']) && (time() - $_SESSION['active'] > 3600 * 24)) {
        unset($_SESSION['signin']);
    }

    // Если пользователь не вошёл, перенаправляем на страницу входа
    if (!signedin()) {
        header('Location: login.php');
        exit();
    }
}
?>