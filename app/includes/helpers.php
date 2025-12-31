<?php
/**
 * Helper Functions
 * Utility functions used throughout the application
 */

/**
 * Redirect to a URL
 * 
 * @param string $url - URL to redirect to
 * @param int $status_code - HTTP status code (default: 302)
 * @return void
 */
function redirect($url, $status_code = 302) {
    header("Location: $url", true, $status_code);
    exit();
}

/**
 * Redirect back to previous page or fallback URL
 * 
 * @param string $fallback - Fallback URL if no referrer exists
 * @return void
 */
function redirect_back($fallback = '/') {
    $url = $_SERVER['HTTP_REFERER'] ?? $fallback;
    redirect($url);
}

/**
 * Set a flash message in session
 * 
 * @param string $type - Message type (success, error, warning, info)
 * @param string $message - Message content
 * @return void
 */
function set_flash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null - ['type' => string, 'message' => string] or null
 */
function get_flash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    
    return null;
}

/**
 * Display flash message as Bootstrap alert
 * 
 * @return void
 */
function display_flash() {
    $flash = get_flash();
    if ($flash) {
        $alert_class = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ][$flash['type']] ?? 'alert-info';
        
        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo escape($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

/**
 * Escape output for HTML (XSS prevention)
 * 
 * @param string $string - String to escape
 * @return string - Escaped string
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input string
 * 
 * @param string $string - String to sanitize
 * @return string - Sanitized string
 */
function sanitize($string) {
    return trim(strip_tags($string));
}

/**
 * Validate email address
 * 
 * @param string $email - Email to validate
 * @return bool
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * Requires: minimum 8 characters, at least one uppercase, one lowercase, one number
 * 
 * @param string $password - Password to validate
 * @return bool
 */
function is_valid_password($password) {
    return strlen($password) >= 8 
        && preg_match('/[A-Z]/', $password) 
        && preg_match('/[a-z]/', $password) 
        && preg_match('/[0-9]/', $password);
}

/**
 * Validate required field
 * 
 * @param mixed $value - Value to check
 * @return bool
 */
function is_required($value) {
    if (is_string($value)) {
        return trim($value) !== '';
    }
    return !empty($value);
}

/**
 * Generate random token
 * 
 * @param int $length - Token length (default: 32)
 * @return string
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Format date for display
 * 
 * @param string $date - Date string
 * @param string $format - Output format (default: 'F j, Y')
 * @return string
 */
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 * 
 * @param string $datetime - Datetime string
 * @param string $format - Output format (default: 'F j, Y g:i A')
 * @return string
 */
function format_datetime($datetime, $format = 'F j, Y g:i A') {
    return date($format, strtotime($datetime));
}

/**
 * Get time ago string (e.g., "2 hours ago")
 * 
 * @param string $datetime - Datetime string
 * @return string
 */
function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = round($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return format_date($datetime);
    }
}

/**
 * Truncate text to specified length
 * 
 * @param string $text - Text to truncate
 * @param int $length - Maximum length (default: 100)
 * @param string $suffix - Suffix to add (default: '...')
 * @return string
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}

/**
 * Format currency amount
 * 
 * @param float $amount - Amount to format
 * @param string $currency - Currency symbol (default: '$')
 * @return string
 */
function format_currency($amount, $currency = '$') {
    return $currency . number_format($amount, 2);
}

/**
 * Check if request is POST
 * 
 * @return bool
 */
function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 * 
 * @return bool
 */
function is_get() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get POST data with optional default value
 * 
 * @param string $key - POST key
 * @param mixed $default - Default value if key doesn't exist
 * @return mixed
 */
function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Get GET data with optional default value
 * 
 * @param string $key - GET key
 * @param mixed $default - Default value if key doesn't exist
 * @return mixed
 */
function get_param($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Debug variable dump (only in development)
 * 
 * @param mixed $var - Variable to dump
 * @param bool $die - Whether to die after dump (default: false)
 * @return void
 */
function dd($var, $die = false) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    if ($die) {
        die();
    }
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generate_token();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string $token - Token to validate
 * @return bool
 */
function csrf_validate($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF input field for forms
 * 
 * @return string
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Get base URL of the application
 * 
 * @return string
 */
function base_url() {
    return '/CourseProject/public';
}

/**
 * Generate URL relative to base
 * 
 * @param string $path - Path relative to base
 * @return string
 */
function url($path = '') {
    return base_url() . '/' . ltrim($path, '/');
}

/**
 * Generate asset URL
 * 
 * @param string $path - Path to asset
 * @return string
 */
function asset($path) {
    return base_url() . '/assets/' . ltrim($path, '/');
}

/**
 * Check if current URL matches given path
 * 
 * @param string $path - Path to check
 * @return bool
 */
function is_active($path) {
    $current = $_SERVER['REQUEST_URI'];
    return strpos($current, $path) !== false;
}

/**
 * Pluralize word based on count
 * 
 * @param int $count - Count
 * @param string $singular - Singular form
 * @param string $plural - Plural form (optional, adds 's' if not provided)
 * @return string
 */
function pluralize($count, $singular, $plural = null) {
    if ($plural === null) {
        $plural = $singular . 's';
    }
    return $count . ' ' . ($count === 1 ? $singular : $plural);
}
