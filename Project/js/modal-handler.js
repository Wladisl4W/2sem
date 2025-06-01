document.addEventListener('DOMContentLoaded', () => {
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));

    const loginForm = document.getElementById('loginForm');
    const openRegistrationButton = document.getElementById('openRegistration');

    // Открытие окна регистрации
    openRegistrationButton.addEventListener('click', () => {
        loginModal.hide();
        registrationModal.show();
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

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    loginModal.hide();
                    editModal.show();
                    // Заполнить форму редактирования данными пользователя
                    populateEditForm(result.userData);
                } else {
                    alert('Ошибка входа: ' + result.message);
                }
            } else {
                alert('Ошибка сервера. Попробуйте позже.');
            }
        } catch (error) {
            alert('Ошибка сети. Попробуйте позже.');
        }
    });

    // Заполнение формы редактирования
    function populateEditForm(userData) {
        const editForm = document.getElementById('editForm');
        for (const [key, value] of Object.entries(userData)) {
            const input = editForm.querySelector(`[name="${key}"]`);
            if (input) input.value = value;
        }
    }
});
