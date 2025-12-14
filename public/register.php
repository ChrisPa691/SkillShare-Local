<?php
session_start();
require_once '../app/config/database.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Server-side validation
    if (empty($full_name) || empty($email) || empty($city) || empty($password) || empty($role)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($role, ['learner', 'instructor'])) {
        $error = 'Invalid role selected.';
    } elseif (!$terms) {
        $error = 'You must agree to the terms and conditions.';
    } else {
        // Check if email already exists
        $existing_user = db_select_one('Users', ['email' => $email]);
        
        if ($existing_user) {
            $error = 'Email address already registered.';
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user into database
            $user_id = db_insert('Users', [
                'full_name' => $full_name,
                'email' => $email,
                'password_hash' => $password_hash,
                'role' => $role,
                'city' => $city
            ]);
            
            if ($user_id) {
                $success = 'Registration successful! Redirecting to login...';
                // Redirect after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SkillShare Local</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 0;
        }
        
        .register-container {
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .register-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .register-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .register-body {
            padding: 40px 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-label i {
            color: #667eea;
            margin-right: 5px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group-text {
            background: transparent;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #667eea;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #667eea;
            z-index: 10;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #764ba2;
        }
        
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .role-card {
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            background: white;
        }
        
        .role-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }
        
        .role-card input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .role-card input[type="radio"]:checked + .role-content {
            color: #667eea;
        }
        
        .role-card input[type="radio"]:checked ~ .role-checkmark {
            opacity: 1;
            transform: scale(1);
        }
        
        .role-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        }
        
        .role-icon {
            font-size: 40px;
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .role-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .role-description {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        
        .role-checkmark {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background: #e0e0e0;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength-text {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-label {
            color: #666;
            font-size: 13px;
        }
        
        .form-check-label a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-check-label a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            color: #999;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #764ba2;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .logo-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .role-selector {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Create Account</h2>
                <p>Join our community and start learning today!</p>
            </div>
            
            <div class="register-body">
                <!-- Alert for messages -->
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo escape($error); ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo escape($success); ?>
                </div>
                <?php endif; ?>
                
                <form id="registerForm" method="POST" action="" novalidate>
                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-user-tag"></i> I want to register as:
                        </label>
                        <div class="role-selector">
                            <label class="role-card" id="learnerCard">
                                <input type="radio" name="role" value="learner" required>
                                <div class="role-content">
                                    <div class="role-icon">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
                                    <div class="role-title">Learner</div>
                                    <p class="role-description">Discover and join workshops</p>
                                </div>
                                <div class="role-checkmark">
                                    <i class="fas fa-check"></i>
                                </div>
                            </label>
                            
                            <label class="role-card" id="instructorCard">
                                <input type="radio" name="role" value="instructor" required>
                                <div class="role-content">
                                    <div class="role-icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div class="role-title">Instructor</div>
                                    <p class="role-description">Create and host sessions</p>
                                </div>
                                <div class="role-checkmark">
                                    <i class="fas fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block" id="roleError" style="display: none !important;">
                            Please select a role.
                        </div>
                    </div>
                    
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="fullName" class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="fullName" 
                                name="full_name" 
                                placeholder="Enter your full name"
                                value="<?php echo isset($_POST['full_name']) ? escape($_POST['full_name']) : ''; ?>"
                                required
                                minlength="3"
                            >
                        </div>
                        <div class="invalid-feedback">
                            Please enter your full name (at least 3 characters).
                        </div>
                    </div>
                    <!-- Email and City -->
                    <div class="form-row mb-3">
                        <div>
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="your@email.com"
                                value="<?php echo isset($_POST['email']) ? escape($_POST['email']) : ''; ?>"
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>
                        
                        <div>
                            <label for="city" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> City
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="city" 
                                name="city" 
                                placeholder="Your city"
                                value="<?php echo isset($_POST['city']) ? escape($_POST['city']) : ''; ?>"
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter your city.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group position-relative">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Create a strong password"
                                required
                                minlength="8"
                            >
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                        <div class="invalid-feedback">
                            Password must be at least 8 characters long.
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <div class="input-group position-relative">
                            <span class="input-group-text">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirmPassword" 
                                name="confirm_password" 
                                placeholder="Confirm your password"
                                required
                            >
                            <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                        </div>
                        <div class="invalid-feedback" id="confirmPasswordError">
                            Passwords do not match.
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" onclick="event.preventDefault()">Terms & Conditions</a> and <a href="#" onclick="event.preventDefault()">Privacy Policy</a>
                            </label>
                            <div class="invalid-feedback">
                                You must agree to the terms and conditions.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Register Button -->
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="divider">
                    <span>OR</span>
                </div>
                
                <!-- Login Link -->
                <div class="login-link">
                    Already have an account? <a href="login.php">Login Here</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
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
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password strength indicator
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
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
        
        // Confirm password validation
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            const errorDiv = document.getElementById('confirmPasswordError');
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
                errorDiv.textContent = 'Passwords do not match.';
            } else {
                this.setCustomValidity('');
                errorDiv.textContent = 'Passwords do not match.';
            }
        });
        
        // Form validation
        const registerForm = document.getElementById('registerForm');
        
        registerForm.addEventListener('submit', function(event) {
            // Remove previous validation
            registerForm.classList.remove('was-validated');
            
            // Check role selection
            const roleSelected = document.querySelector('input[name="role"]:checked');
            const roleError = document.getElementById('roleError');
            
            if (!roleSelected) {
                event.preventDefault();
                roleError.style.display = 'block';
                roleError.style.color = '#dc3545';
                roleError.style.fontSize = '14px';
                roleError.style.marginTop = '5px';
                return;
            } else {
                roleError.style.display = 'none';
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
        
        // Show alert function
        function showAlert(message, type = 'danger') {
            const alertDiv = document.getElementById('registerAlert');
            const alertMessage = document.getElementById('alertMessage');
            
            alertDiv.className = `alert alert-${type}`;
            alertMessage.textContent = message;
            alertDiv.classList.remove('d-none');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                alertDiv.classList.add('d-none');
            }, 5000);
        }
        
        // Email validation on input
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('input', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>