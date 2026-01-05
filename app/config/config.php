<?php
/**
 * ============================================================
 * Application Configuration - SkillShare Local
 * ============================================================
 * 
 * THIS FILE CONTAINS SERVER-ONLY, SENSITIVE CONFIGURATION
 * 
 * ⚠️  WHAT BELONGS HERE:
 * - Database credentials
 * - Application secrets/salts
 * - Environment settings (development/production)
 * - Server paths and file system configuration
 * - Third-party API keys (SMTP, payment gateways, etc.)
 * - Error reporting and debugging flags
 * 
 * ❌ WHAT DOES NOT BELONG HERE:
 * - User-editable application settings
 * - Runtime behavior configuration
 * - UI/UX settings
 * - Business logic settings (booking rules, rating scales, etc.)
 * 
 * 👉 For user-editable settings, use the database table: app_settings
 *    Managed via Settings model (app/models/Settings.php)
 * 
 * ============================================================
 */

// Allow inclusion of secure files
define('ALLOW_INCLUDE', true);

// ============================================================
// ENVIRONMENT CONFIGURATION
// ============================================================
define('APP_ENV', 'development'); // 'development' or 'production'
define('APP_DEBUG', true);         // Enable detailed error messages

// Set error reporting based on environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../logs/php-errors.log');
}

// ============================================================
// DATABASE CONFIGURATION (SENSITIVE - DO NOT STORE IN DB)
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'skilshopdb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ============================================================
// APPLICATION PATHS (SERVER-SPECIFIC)
// ============================================================
define('BASE_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/assets/images/sessions');
define('LOGS_PATH', BASE_PATH . '/logs');

// ============================================================
// APPLICATION URL (ENVIRONMENT-SPECIFIC)
// ============================================================
define('BASE_URL', 'http://localhost/CourseProject');
define('PUBLIC_URL', BASE_URL . '/public');

// ============================================================
// SECURITY CONFIGURATION (SENSITIVE)
// ============================================================
// Application secret for CSRF tokens, password hashing salt, etc.
define('APP_SECRET', 'your-secret-key-change-in-production');

// Session configuration
define('SESSION_NAME', 'SKILLSHARE_SESSION');
define('SESSION_COOKIE_LIFETIME', 0); // 0 = until browser closes

// ============================================================
// TIMEZONE
// ============================================================
date_default_timezone_set('Europe/London');

// ============================================================
// DATABASE CONNECTION
// ============================================================
try {
    // Create PDO connection
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die("Database Connection Failed: " . $e->getMessage());
    } else {
        die("A database error occurred. Please contact support.");
    }
}

// ============================================================
// INCLUDE DEPENDENCIES
// ============================================================
require_once __DIR__ . '/cookies.php';

// ============================================================
// OPTIONAL: EMAIL/SMTP CONFIGURATION (if needed in future)
// ============================================================
// define('SMTP_HOST', 'smtp.example.com');
// define('SMTP_PORT', 587);
// define('SMTP_USER', 'noreply@skillshare.local');
// define('SMTP_PASS', 'your-smtp-password');
// define('SMTP_FROM', 'noreply@skillshare.local');
// define('SMTP_FROM_NAME', 'SkillShare Local');

// ============================================================
// OPTIONAL: THIRD-PARTY API KEYS (if needed)
// ============================================================
// define('STRIPE_SECRET_KEY', 'sk_test_...');
// define('GOOGLE_MAPS_API_KEY', 'AIza...');

?>