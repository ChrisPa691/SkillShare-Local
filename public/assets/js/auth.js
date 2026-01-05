/**
 * Auth JavaScript - Login and Register Pages
 * Handles authentication form interactions
 */

// ====================================
// LOGIN PAGE FUNCTIONS
// ====================================

function initLoginPage() {
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
    
    // Form validation
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            // Remove previous validation
            loginForm.classList.remove('was-validated');
            
            // Check validity
            if (!loginForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                loginForm.classList.add('was-validated');
                return;
            }
            
            // Add loading state to button
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Logging in...';
            submitBtn.disabled = true;
            
            // Allow form to submit normally to server
        });
    }
    
    // Email validation on input
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Add animation on input focus
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
}

// Show alert function for login
function showLoginAlert(message, type = 'danger') {
    const alertDiv = document.getElementById('loginAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    if (alertDiv && alertMessage) {
        alertDiv.className = `alert alert-${type}`;
        alertMessage.textContent = message;
        alertDiv.classList.remove('d-none');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertDiv.classList.add('d-none');
        }, 5000);
    }
}

// ====================================
// REGISTER PAGE FUNCTIONS
// ====================================

function initRegisterPage() {
    // Role card selection
    const roleCards = document.querySelectorAll('.role-card');
    const roleInputs = document.querySelectorAll('input[name="role"]');
    
    roleCards.forEach(card => {
        card.addEventListener('click', function() {
            roleCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    
    roleInputs.forEach(input => {
        input.addEventListener('change', function() {
            roleCards.forEach(c => c.classList.remove('selected'));
            this.closest('.role-card').classList.add('selected');
        });
    });
    
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
    
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
    
    // Password strength indicator
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            if (!strengthBar || !strengthText) return;
            
            let strength = 0;
            let strengthLabel = '';
            let strengthColor = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const percentage = (strength / 4) * 100;
            strengthBar.style.width = percentage + '%';
            
            switch(strength) {
                case 0:
                case 1:
                    strengthLabel = 'Weak';
                    strengthColor = '#dc3545';
                    break;
                case 2:
                    strengthLabel = 'Fair';
                    strengthColor = '#ffc107';
                    break;
                case 3:
                    strengthLabel = 'Good';
                    strengthColor = '#17a2b8';
                    break;
                case 4:
                    strengthLabel = 'Strong';
                    strengthColor = '#28a745';
                    break;
            }
            
            strengthBar.style.backgroundColor = strengthColor;
            strengthText.style.color = strengthColor;
            strengthText.textContent = password.length > 0 ? strengthLabel : '';
        });
    }
    
    // Confirm password validation
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            const errorDiv = document.getElementById('confirmPasswordError');
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
                if (errorDiv) errorDiv.textContent = 'Passwords do not match.';
            } else {
                this.setCustomValidity('');
                if (errorDiv) errorDiv.textContent = 'Passwords do not match.';
            }
        });
    }
    
    // Form validation
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            // Remove previous validation
            registerForm.classList.remove('was-validated');
            
            // Check role selection
            const roleSelected = document.querySelector('input[name="role"]:checked');
            const roleError = document.getElementById('roleError');
            
            if (!roleSelected) {
                event.preventDefault();
                if (roleError) {
                    roleError.style.display = 'block';
                    roleError.style.color = '#dc3545';
                    roleError.style.fontSize = '14px';
                    roleError.style.marginTop = '5px';
                }
                return;
            } else {
                if (roleError) roleError.style.display = 'none';
            }
            
            // Check password match
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                confirmPasswordInput.setCustomValidity('Passwords do not match');
                registerForm.classList.add('was-validated');
                return;
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
            
            // Check overall validity
            if (!registerForm.checkValidity()) {
                event.preventDefault();
                registerForm.classList.add('was-validated');
                return;
            }
            
            // If all validation passes, allow form to submit normally
            // The PHP backend will handle the actual submission
        });
    }
    
    // Email validation on input
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    }
}

// Show alert function for register
function showRegisterAlert(message, type = 'danger') {
    const alertDiv = document.getElementById('registerAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    if (alertDiv && alertMessage) {
        alertDiv.className = `alert alert-${type}`;
        alertMessage.textContent = message;
        alertDiv.classList.remove('d-none');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertDiv.classList.add('d-none');
        }, 5000);
    }
}

// ====================================
// PAGE INITIALIZATION
// ====================================

document.addEventListener('DOMContentLoaded', function() {
    // Detect which page we're on and initialize accordingly
    if (document.getElementById('loginForm')) {
        initLoginPage();
    }
    
    if (document.getElementById('registerForm')) {
        initRegisterPage();
    }
});
