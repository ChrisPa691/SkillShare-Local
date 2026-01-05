<?php
/**
 * Authentication Controller
 * Handles user authentication operations (login, register, logout)
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/config.php';

class AuthController {
    
    /**
     * Handle user registration
     * 
     * @return array - ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public static function handleRegister() {
        // Validate request method
        if (!is_post()) {
            return [
                'success' => false,
                'message' => 'Invalid request method.'
            ];
        }
        
        // Get and sanitize form data
        $full_name = trim(post('full_name'));
        $email = trim(post('email'));
        $city = trim(post('city'));
        $password = post('password');
        $confirm_password = post('confirm_password');
        $role = post('role');
        $terms = post('terms') !== null;
        
        // Validate required fields
        if (!is_required($full_name) || !is_required($email) || !is_required($city) || 
            !is_required($password) || !is_required($role)) {
            return [
                'success' => false,
                'message' => 'All fields are required.'
            ];
        }
        
        // Validate email format
        if (!is_valid_email($email)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        // Validate password strength
        if (!is_valid_password($password)) {
            return [
                'success' => false,
                'message' => 'Password must be at least 8 characters long.'
            ];
        }
        
        // Validate password confirmation
        if ($password !== $confirm_password) {
            return [
                'success' => false,
                'message' => 'Passwords do not match.'
            ];
        }
        
        // Validate role
        if (!in_array($role, ['learner', 'instructor'])) {
            return [
                'success' => false,
                'message' => 'Invalid role selected.'
            ];
        }
        
        // Validate terms acceptance
        if (!$terms) {
            return [
                'success' => false,
                'message' => 'You must agree to the terms and conditions.'
            ];
        }
        
        // Check if email already exists
        if (User::emailExists($email)) {
            return [
                'success' => false,
                'message' => 'Email address already registered.'
            ];
        }
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Create user
        $user_id = User::createUser([
            'full_name' => $full_name,
            'email' => $email,
            'password_hash' => $password_hash,
            'role' => $role,
            'city' => $city
        ]);
        
        if ($user_id) {
            return [
                'success' => true,
                'message' => 'Registration successful! Redirecting to login...',
                'user_id' => $user_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }
    
    /**
     * Handle user login
     * 
     * @return array - ['success' => bool, 'message' => string, 'redirect' => string|null]
     */
    public static function handleLogin() {
        // Validate request method
        if (!is_post()) {
            return [
                'success' => false,
                'message' => 'Invalid request method.'
            ];
        }
        
        // Get and sanitize form data
        $email = trim(post('email'));
        $password = post('password');
        $remember = post('remember') !== null;
        
        // Validate required fields
        if (!is_required($email) || !is_required($password)) {
            return [
                'success' => false,
                'message' => 'Email and password are required.'
            ];
        }
        
        // Validate email format
        if (!is_valid_email($email)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.'
            ];
        }
        
        // Find user by email
        $user = User::getUserByEmail($email);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
        
        // Check if account is suspended
        if ($user['is_suspended']) {
            $reason = $user['suspended_reason'] ?? 'No reason provided';
            return [
                'success' => false,
                'message' => "Your account has been suspended. Reason: $reason"
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
        
        // Password correct - create session
        self::createUserSession($user);
        
        // Handle "remember me" functionality (optional)
        if ($remember) {
            // Could implement remember me cookie here
            // For now, we'll just extend the session lifetime
            ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30); // 30 days
        }
        
        return [
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => 'dashboard.php'
        ];
    }
    
    /**
     * Handle user logout
     * 
     * @return array - ['success' => bool, 'message' => string, 'redirect' => string]
     */
    public static function handleLogout() {
        // Destroy session
        self::destroyUserSession();
        
        return [
            'success' => true,
            'message' => 'You have been logged out successfully.',
            'redirect' => 'login.php'
        ];
    }
    
    /**
     * Create user session
     * 
     * @param array $user - User data
     * @return void
     */
    private static function createUserSession($user) {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['city'] = $user['city'] ?? null;
        $_SESSION['last_activity'] = time();
        
        // Create authentication cookie
        createAuthCookie(session_id());
    }
    
    /**
     * Destroy user session
     * 
     * @return void
     */
    private static function destroyUserSession() {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Delete authentication cookie
        deleteAuthCookie();
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current authenticated user
     * 
     * @return array|null - User data or null if not authenticated
     */
    public static function getCurrentUser() {
        if (!self::isAuthenticated()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'city' => $_SESSION['city'] ?? null
        ];
    }
    
    /**
     * Check if user has specific role
     * 
     * @param string $role - Role to check
     * @return bool
     */
    public static function hasRole($role) {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Validate session timeout (optional security feature)
     * 
     * @param int $timeout - Timeout in seconds (default: 30 minutes)
     * @return bool - True if session is still valid
     */
    public static function validateSessionTimeout($timeout = 1800) {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        if (isset($_SESSION['last_activity'])) {
            $elapsed_time = time() - $_SESSION['last_activity'];
            
            if ($elapsed_time > $timeout) {
                self::destroyUserSession();
                return false;
            }
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Change user password
     * 
     * @param int $user_id - User ID
     * @param string $current_password - Current password
     * @param string $new_password - New password
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function changePassword($user_id, $current_password, $new_password) {
        // Get user
        $user = User::getUserById($user_id);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }
        
        // Verify current password
        if (!password_verify($current_password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect.'
            ];
        }
        
        // Validate new password
        if (!is_valid_password($new_password)) {
            return [
                'success' => false,
                'message' => 'New password must be at least 8 characters long.'
            ];
        }
        
        // Hash new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $success = User::updateUser($user_id, ['password_hash' => $new_password_hash]);
        
        if ($success) {
            return [
                'success' => true,
                'message' => 'Password changed successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update password. Please try again.'
            ];
        }
    }
}
