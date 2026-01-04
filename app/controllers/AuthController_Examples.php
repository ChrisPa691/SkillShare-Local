<?php
/**
 * AuthController Usage Examples
 * Demonstrates how to use the refactored authentication system
 */

// This file shows examples - NOT meant to be executed directly
// Copy these patterns into your actual pages (login.php, register.php, etc.)

// ============================================================================
// EXAMPLE 1: Using AuthController in login.php
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';
require_once '../app/includes/helpers.php';

// Redirect if already logged in
if (AuthController::isAuthenticated()) {
    redirect('dashboard.php');
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = AuthController::handleLogin();
    
    if ($result['success']) {
        redirect($result['redirect']);
    } else {
        $error = $result['message'];
    }
}
// Then your HTML form here...
?>
*/

// ============================================================================
// EXAMPLE 2: Using AuthController in register.php
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';
require_once '../app/includes/helpers.php';

// Redirect if already logged in
if (AuthController::isAuthenticated()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = AuthController::handleRegister();
    
    if ($result['success']) {
        $success = $result['message'];
        header("refresh:2;url=login.php");
    } else {
        $error = $result['message'];
    }
}
// Then your HTML form here...
?>
*/

// ============================================================================
// EXAMPLE 3: Using AuthController in logout.php
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';

$result = AuthController::handleLogout();
header("Location: " . $result['redirect']);
exit();
?>
*/

// ============================================================================
// EXAMPLE 4: Checking authentication in protected pages
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';

// Redirect to login if not authenticated
if (!AuthController::isAuthenticated()) {
    header("Location: login.php");
    exit();
}

// Get current user data
$current_user = AuthController::getCurrentUser();
echo "Welcome, " . $current_user['full_name'];
?>
*/

// ============================================================================
// EXAMPLE 5: Role-based access control
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';

// Require authentication
if (!AuthController::isAuthenticated()) {
    header("Location: login.php");
    exit();
}

// Require admin role
if (!AuthController::hasRole('admin')) {
    die("Access denied. Admin role required.");
}

// Admin-only code here...
?>
*/

// ============================================================================
// EXAMPLE 6: Session timeout validation
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';

// Check if session has timed out (30 minutes = 1800 seconds)
if (!AuthController::validateSessionTimeout(1800)) {
    header("Location: login.php?timeout=1");
    exit();
}

// Continue with page logic...
?>
*/

// ============================================================================
// EXAMPLE 7: Change password functionality
// ============================================================================
/*
<?php
session_start();
require_once '../app/controllers/AuthController.php';

if (!AuthController::isAuthenticated()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    $result = AuthController::changePassword($user_id, $current_password, $new_password);
    
    if ($result['success']) {
        echo "Success: " . $result['message'];
    } else {
        echo "Error: " . $result['message'];
    }
}
?>
*/

// ============================================================================
// EXAMPLE 8: Using User model directly (for admin functions)
// ============================================================================
/*
<?php
require_once '../app/models/User.php';

// Get all instructors
$instructors = User::getUsersByRole('instructor');

// Check if email exists
if (User::emailExists('test@example.com')) {
    echo "Email already registered";
}

// Suspend a user
User::suspendUser(5, "Violation of terms of service");

// Unsuspend a user
User::unsuspendUser(5);

// Get specific user
$user = User::getUserById(1);
echo $user['full_name'];
?>
*/

echo "This file contains usage examples only. See the code for implementation patterns.";
