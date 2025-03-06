document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('PASSWORD');
    const confirmPasswordInput = document.getElementById('CONFIRM_PASSWORD');
    const passwordToggle = document.getElementById('password-toggle');
    const confirmPasswordToggle = document.getElementById('confirm-password-toggle'); 

    passwordToggle.addEventListener('click', function () {
        togglePasswordVisibility(passwordInput, passwordToggle);
    });

    confirmPasswordToggle.addEventListener('click', function () {
        togglePasswordVisibility(confirmPasswordInput, confirmPasswordToggle);
    });

    function togglePasswordVisibility(input, toggleIcon) {
        if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
});
