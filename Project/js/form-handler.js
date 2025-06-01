document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#registrationForm');

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.lang = formData.getAll('lang[]'); // Обработка чекбоксов

            try {
                const response = await fetch('backend/register.php', {
                    method: 'POST',
                    body: new URLSearchParams(data),
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(`Регистрация успешна! Ваш логин: ${result.login}, пароль: ${result.password}`);
                    form.reset();
                } else {
                    const error = await response.json();
                    alert('Ошибка: ' + (error.errors ? JSON.stringify(error.errors) : error.error));
                }
            } catch (error) {
                alert('Ошибка сети. Попробуйте позже.');
            }
        });
    }
});
