document.addEventListener('DOMContentLoaded', function() {
    // Validasi Form Registrasi
    const registerForm = document.querySelector('form[action="register.php"]');
    if (registerForm) {
        const passwordInput = registerForm.querySelector('input[name="password"]');
        const confirmPasswordInput = registerForm.querySelector('input[name="password_confirm"]');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert error';
        errorDiv.style.display = 'none';
        registerForm.prepend(errorDiv);

        registerForm.addEventListener('submit', function(event) {
            let errors = [];
            
            if (passwordInput.value.length < 8) {
                errors.push('Password harus minimal 8 karakter.');
            }
            
            if (passwordInput.value !== confirmPasswordInput.value) {
                errors.push('Konfirmasi password tidak cocok.');
            }

            if (errors.length > 0) {
                event.preventDefault(); 
                errorDiv.innerHTML = errors.join('<br>');
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        });
    }

    // Konfirmasi penghapusan
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        const originalOnclick = link.getAttribute('onclick');
        link.addEventListener('click', function(e) {
            if (!confirm('Anda yakin ingin melakukan tindakan ini?')) {
                e.preventDefault();
            }
        });
        link.removeAttribute('onclick');
    });
});
