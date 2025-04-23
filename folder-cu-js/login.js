document.addEventListener('DOMContentLoaded', function() {

    const togglePassword = document.querySelector('.toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function(e) {
            e.preventDefault();
            const passwordInput = document.getElementById('password');
            if (!passwordInput) return;

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            const icons = {
                eye: this.querySelector('.fa-eye'),
                eyeSlash: this.querySelector('.fa-eye-slash')
            };

            if (icons.eye && icons.eyeSlash) {
                icons.eye.style.display = isPassword ? 'none' : 'inline-block';
                icons.eyeSlash.style.display = isPassword ? 'inline-block' : 'none';
            }

            passwordInput.focus();
        });
    }

    const loginForm = document.querySelector('form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]');
            const password = document.getElementById('password');

            if (!email?.value.trim() || !password?.value.trim()) {
                e.preventDefault();

                const errorMsg = document.querySelector('.error-message') || document.createElement('div');
                errorMsg.className = 'error-message';
                errorMsg.textContent = 'Te rugăm să completezi toate câmpurile!';
                errorMsg.style.color = '#feb2b2';
                errorMsg.style.marginTop = '10px';
                errorMsg.style.textAlign = 'center';

                if (!document.querySelector('.error-message')) {
                    loginForm.appendChild(errorMsg);
                }

                (!email.value.trim() ? email : password).focus();
            }
        });
    }

    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('.btn').click();
            }
        });
    }
});