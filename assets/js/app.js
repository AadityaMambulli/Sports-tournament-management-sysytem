document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            mainNav.classList.toggle('nav-open');
        });
    }

    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const target = document.getElementById(button.dataset.target);
            if (!target) return;
            const isPassword = target.type === 'password';
            target.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'Hide' : 'Show';
        });
    });

    const alertBox = document.querySelector('.alert');
    if (alertBox) {
        setTimeout(function () {
            alertBox.style.display = 'none';
        }, 4000);
    }
});
