document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Остановить стандартное поведение формы

        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('Успешно')) {
                alert('Форма успешно отправлена!');
            } else {
                alert('Произошла ошибка при отправке формы:\n' + data);
            }
        })
        .catch(error => {
            alert('Произошла ошибка при отправке формы.');
        });
    });
});