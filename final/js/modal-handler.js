document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');

    // Обработка формы входа
    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(loginForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('back/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            if (result.success) {
                // Успешный вход
                loginError.classList.add('d-none'); // Скрываем сообщение об ошибке
                window.location.href = 'back/edit.php'; // Переход на страницу редактирования
            } else {
                // Ошибка входа
                loginError.textContent = 'Неверный логин или пароль.';
                loginError.classList.remove('d-none'); // Показываем сообщение об ошибке
            }
        } catch (error) {
            console.error('Ошибка сети:', error);
            loginError.textContent = 'Ошибка сети. Попробуйте позже.';
            loginError.classList.remove('d-none'); // Показываем сообщение об ошибке
        }
    });
});
