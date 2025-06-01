document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const openRegistrationButton = document.getElementById('openRegistration');

    // Перенаправление на страницу регистрации
    if (openRegistrationButton) {
        console.log('Кнопка "Зарегистрироваться" найдена.');
        openRegistrationButton.addEventListener('click', () => {
            console.log('Кнопка "Зарегистрироваться" нажата.');
            window.location.href = 'backend/register.php'; // Переход на страницу регистрации
        });
    } else {
        console.error('Кнопка "Зарегистрироваться" не найдена.');
    }

    // Обработка формы входа
    if (loginForm) {
        console.log('Форма входа найдена.');
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('backend/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });

                const result = await response.json();
                if (result.success) {
                    console.log('Вход выполнен успешно.');
                    window.location.href = 'backend/edit.php'; // Переход на страницу редактирования
                } else {
                    alert('Ошибка входа: ' + result.message);
                }
            } catch (error) {
                console.error('Ошибка сети:', error);
                alert('Ошибка сети. Попробуйте позже.');
            }
        });
    } else {
        console.error('Форма входа не найдена.');
    }
});
