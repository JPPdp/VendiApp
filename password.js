document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('PASSWORD');
    const confirmPasswordInput = document.getElementById('CONFIRM_PASSWORD');
    const passwordToggle = document.getElementById('password-toggle');
    const confirmPasswordToggle = document.getElementById('confirm-password-toggle');
    const PASSWORD_REQUIREMENTS = document.getElementById('PASSWORD_REQUIREMENTS');

    // Toggle password visibility for the password field
    passwordToggle.addEventListener('click', function () {
        togglePasswordVisibility(passwordInput, passwordToggle);
    });

    // Toggle password visibility for the confirm password field
    confirmPasswordToggle.addEventListener('click', function () {
        togglePasswordVisibility(confirmPasswordInput, confirmPasswordToggle);
    });

    // Show password requirements dropdown when password field is focused
    passwordInput.addEventListener('focus', function () {
        PASSWORD_REQUIREMENTS.style.display = 'block'; // Show the dropdown
    });

    // Hide password requirements dropdown when clicking outside the password input
    document.addEventListener('click', function (event) {
        const isClickInsidePasswordInput = passwordInput.contains(event.target);
        const isClickInsideDropdown = PASSWORD_REQUIREMENTS.contains(event.target);

        if (!isClickInsidePasswordInput && !isClickInsideDropdown) {
            PASSWORD_REQUIREMENTS.style.display = 'none'; // Hide the dropdown
        }
    });

    // Function to toggle password visibility
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


function confirmDelete() {
    if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) 
        {
        window.location.href = 'delete_account.php';
    }
}