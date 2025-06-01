document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#registrationForm');

    if (form) {
        form.addEventListener('submit', (event) => {
            const errors = [];
            const fio = form.querySelector('input[name="FIO"]').value.trim();
            const tel = form.querySelector('input[name="tel"]').value.trim();
            const email = form.querySelector('input[name="email"]').value.trim();
            const bio = form.querySelector('textarea[name="bio"]').value.trim();
            const langs = form.querySelectorAll('input[name="lang[]"]:checked');

            // Валидация ФИО
            if (!fio || fio.length > 150 || !/^[а-яА-ЯёЁa-zA-Z\s\-]+$/.test(fio)) {
                errors.push('ФИО должно быть заполнено, содержать только буквы, пробелы и дефисы, и не превышать 150 символов.');
            }

            // Валидация телефона
            if (!tel || !/^\+?[0-9]{10,15}$/.test(tel)) {
                errors.push('Введите корректный номер телефона (только цифры, от 10 до 15 символов).');
            }

            // Валидация email
            if (!email || !/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
                errors.push('Введите корректный email.');
            }

            // Валидация биографии
            if (!bio || bio.length > 1000) {
                errors.push('Биография должна быть заполнена и не превышать 1000 символов.');
            }

            // Валидация языков программирования
            if (langs.length === 0) {
                errors.push('Выберите хотя бы один язык программирования.');
            }

            if (errors.length > 0) {
                event.preventDefault();
                alert(errors.join('\n'));
            }
        });
    }
});
