<?php
/**
 * Authentication Guard
 * Protects pages from unauthorized access
 */

// Include cookie functions
require_once __DIR__ . '/../config/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 * 
 * @return void
 */
function require_login() {
    // First check if session exists
    if (!isset($_SESSION['user_id'])) {
        // Try to validate and restore from cookie
        if (validateAuthCookie() && isset($_SESSION['user_id'])) {
            // Cookie validation successful, session restored
            return;
        }
        
        // No valid session or cookie - redirect to login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /CourseProject/public/login.php');
        exit();
    }
    
    // Session exists - validate cookie matches
    if (!validateAuthCookie()) {
        // Cookie invalid or missing - handle expired cookie
        handleExpiredCookie(AUTH_COOKIE_NAME);
    }
}

/**
 * Require specific role(s)
 * Redirects to appropriate page if user doesn't have required role
 * 
 * @param string|array $allowed_roles - Single role or array of allowed roles
 * @return void
 */
function require_role($allowed_roles) {
    // First ensure user is logged in
    require_login();
    
    // Convert single role to array
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    // Check if user has required role
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // Redirect based on current role
        $role = $_SESSION['role'];
        header("Location: /CourseProject/public/{$role}_dashboard.php");
        exit();
    }
}

/**
 * Check if user is logged in (without redirecting)
 * 
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 * 
 * @param string $role - Role to check
 * @return bool
 */
function has_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Check if user is admin
 * 
 * @return bool
 */
function is_admin() {
    return has_role('admin');
}

/**
 * Check if user is instructor
 * 
 * @return bool
 */
function is_instructor() {
    return has_role('instructor');
}

/**
 * Check if user is learner
 * 
 * @return bool
 */
function is_learner() {
    return has_role('learner');
}

/**
 * Get current user ID
 * 
 * @return int|null
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's full name
 * 
 * @return string|null
 */
function get_user_name() {
    return $_SESSION['full_name'] ?? null;
}

/**
 * Get current user's email
 * 
 * @return string|null
 */
function get_user_email() {
    return $_SESSION['email'] ?? null;
}

/**
 * Get current user's role
 * 
 * @return string|null
 */
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user's city
 * 
 * @return string|null
 */
function get_user_city() {
    return $_SESSION['city'] ?? null;
}

/**
 * Redirect guest users to login
 * Use this on pages that should only show content to logged-in users
 * 
 * @return void
 */
function guest_only() {
    if (is_logged_in()) {
        $role = $_SESSION['role'];
        header("Location: /CourseProject/public/{$role}_dashboard.php");
        exit();
    }
}
