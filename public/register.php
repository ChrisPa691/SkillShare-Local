<?php
session_start();
require_once '../app/controllers/AuthController.php';
require_once '../app/includes/helpers.php';

$error = '';
$success = '';

// Redirect if already logged in
if (AuthController::isAuthenticated()) {
    redirect('dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = AuthController::handleRegister();
    
    if ($result['success']) {
        $success = $result['message'];
        // Redirect after 2 seconds
        header("refresh:2;url=login.php");
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SkillShare Local</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/ui/favicon.svg">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page register-page">
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
    <script src="assets/js/auth.js"></script>
</body>
</html>