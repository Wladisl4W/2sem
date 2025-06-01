document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const openRegistrationButton = document.getElementById('openRegistration');

    // Перенаправление на страницу регистрации
    openRegistrationButton.addEventListener('click', () => {
        window.location.href = 'backend/register.php'; // Переход на страницу регистрации
    });

    // Обработка формы входа
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
                window.location.href = 'backend/edit.php'; // Переход на страницу редактирования
            } else {
                alert('Ошибка входа: ' + result.message);
            }
        } catch (error) {
            alert('Ошибка сети. Попробуйте позже.');
        }
    });
});
