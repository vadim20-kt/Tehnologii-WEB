document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const eyeIcon = this.querySelector('.fa-eye');
            const eyeSlashIcon = this.querySelector('.fa-eye-slash');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeSlashIcon.style.display = 'block';
                input.setAttribute('aria-describedby', 'password-visible-warning');
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'block';
                eyeSlashIcon.style.display = 'none';
                input.removeAttribute('aria-describedby');
            }
        });
    });

    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('password-strength');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('password-match');

    if (passwordInput && passwordStrength) {
        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            passwordStrength.textContent = strength.message;
            passwordStrength.className = 'password-strength ' + strength.class;
        });
    }

    if (confirmPasswordInput && passwordMatch) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && passwordInput.value !== this.value) {
                passwordMatch.textContent = 'Parolele nu coincid!';
                passwordMatch.className = 'password-match mismatch';
            } else if (this.value) {
                passwordMatch.textContent = 'Parolele coincid!';
                passwordMatch.className = 'password-match match';
            } else {
                passwordMatch.textContent = '';
                passwordMatch.className = 'password-match';
            }
        });
    }

    function checkPasswordStrength(password) {
        const strength = {
            0: { message: 'Foarte slabă', class: 'very-weak' },
            1: { message: 'Slabă', class: 'weak' },
            2: { message: 'Moderată', class: 'moderate' },
            3: { message: 'Puternică', class: 'strong' },
            4: { message: 'Foarte puternică', class: 'very-strong' }
        };

        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
        if (password.match(/\d+/)) score++;
        if (password.match(/.[!,@,#,$,%,^,&,*,?,_,~]/)) score++;

        return strength[Math.min(score, 4)];
    }
});