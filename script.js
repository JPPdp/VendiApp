document.addEventListener('DOMContentLoaded', function () {
    const CLIENTbtn = document.getElementById('btnCLIENT');
    const VENDORbtn = document.getElementById('btnVENDOR');

    const CLIENTform = document.getElementById('CLIENT_FORM');
    const VENDORform = document.getElementById('VENDOR_FORM');

    CLIENTform.classList.add('active');
    CLIENTbtn.classList.add('active');

    CLIENTbtn.addEventListener('click', function () {
        CLIENTform.classList.add('active');
        VENDORform.classList.remove('active');

        CLIENTbtn.classList.add('active');
        VENDORbtn.classList.remove('active');
    });

    VENDORbtn.addEventListener('click', function () {
        VENDORform.classList.add('active');
        CLIENTform.classList.remove('active');

        VENDORbtn.classList.add('active');
        CLIENTbtn.classList.remove('active');
    });
});


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