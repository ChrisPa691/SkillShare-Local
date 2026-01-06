<?php
/**
 * Application Constants
 * 
 * Defines all constants used throughout the SkillShare Local application.
 * Centralizes configuration values, status codes, roles, and other magic strings.
 * 
 * @package SkillShareLocal
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(dirname(__DIR__)));
}

/**
 * ==========================================
 * USER ROLES
 * ==========================================
 */
define('ROLE_LEARNER', 'learner');
define('ROLE_INSTRUCTOR', 'instructor');
define('ROLE_ADMIN', 'admin');

// Array of all valid roles
define('VALID_ROLES', [
    ROLE_LEARNER,
    ROLE_INSTRUCTOR,
    ROLE_ADMIN
]);

/**
 * ==========================================
 * SESSION STATUSES
 * ==========================================
 */
define('SESSION_STATUS_UPCOMING', 'upcoming');
define('SESSION_STATUS_COMPLETED', 'completed');
define('SESSION_STATUS_CANCELED', 'canceled');

// Array of all valid session statuses
define('VALID_SESSION_STATUSES', [
    SESSION_STATUS_UPCOMING,
    SESSION_STATUS_COMPLETED,
    SESSION_STATUS_CANCELED
]);

/**
 * ==========================================
 * BOOKING STATUSES
 * ==========================================
 */
define('BOOKING_STATUS_PENDING', 'pending');
define('BOOKING_STATUS_ACCEPTED', 'accepted');
define('BOOKING_STATUS_DECLINED', 'declined');
define('BOOKING_STATUS_CANCELED', 'canceled');

// Array of all valid booking statuses
define('VALID_BOOKING_STATUSES', [
    BOOKING_STATUS_PENDING,
    BOOKING_STATUS_ACCEPTED,
    BOOKING_STATUS_DECLINED,
    BOOKING_STATUS_CANCELED
]);

/**
 * ==========================================
 * LOCATION TYPES
 * ==========================================
 */
define('LOCATION_TYPE_IN_PERSON', 'in-person');
define('LOCATION_TYPE_ONLINE', 'online');

// Array of all valid location types
define('VALID_LOCATION_TYPES', [
    LOCATION_TYPE_IN_PERSON,
    LOCATION_TYPE_ONLINE
]);

/**
 * ==========================================
 * FEE TYPES
 * ==========================================
 */
define('FEE_TYPE_FREE', 'free');
define('FEE_TYPE_PAID', 'paid');

// Array of all valid fee types
define('VALID_FEE_TYPES', [
    FEE_TYPE_FREE,
    FEE_TYPE_PAID
]);

/**
 * ==========================================
 * FLASH MESSAGE TYPES
 * ==========================================
 */
define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR', 'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO', 'info');
define('FLASH_DANGER', 'danger'); // Alias for error

// Array of all valid flash message types
define('VALID_FLASH_TYPES', [
    FLASH_SUCCESS,
    FLASH_ERROR,
    FLASH_WARNING,
    FLASH_INFO,
    FLASH_DANGER
]);

/**
 * ==========================================
 * FILE UPLOAD SETTINGS
 * ==========================================
 */
// Maximum file sizes (in bytes)
define('MAX_AVATAR_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_SESSION_IMAGE_SIZE', 10 * 1024 * 1024); // 10MB

// Allowed image MIME types
define('ALLOWED_IMAGE_TYPES', [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/gif',
    'image/webp'
]);

// Allowed image extensions
define('ALLOWED_IMAGE_EXTENSIONS', [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'webp'
]);

/**
 * ==========================================
 * DIRECTORY PATHS
 * ==========================================
 */
define('DIR_UPLOADS', APP_ROOT . '/public/uploads');
define('DIR_AVATARS', DIR_UPLOADS . '/avatars');
define('DIR_SESSION_IMAGES', DIR_UPLOADS . '/sessions');
define('DIR_ASSETS', APP_ROOT . '/public/assets');
define('DIR_CSS', DIR_ASSETS . '/css');
define('DIR_JS', DIR_ASSETS . '/js');
define('DIR_IMAGES', DIR_ASSETS . '/images');

/**
 * ==========================================
 * URL PATHS
 * ==========================================
 */
define('URL_BASE', '/SkillShare-Local/public');
define('URL_UPLOADS', URL_BASE . '/uploads');
define('URL_AVATARS', URL_UPLOADS . '/avatars');
define('URL_SESSION_IMAGES', URL_UPLOADS . '/sessions');
define('URL_ASSETS', URL_BASE . '/assets');

/**
 * ==========================================
 * PAGINATION SETTINGS
 * ==========================================
 */
define('ITEMS_PER_PAGE', 20);
define('SESSIONS_PER_PAGE', 12);
define('BOOKINGS_PER_PAGE', 15);
define('USERS_PER_PAGE', 25);

/**
 * ==========================================
 * VALIDATION LIMITS
 * ==========================================
 */
// Text field lengths
define('MIN_PASSWORD_LENGTH', 8);
define('MAX_PASSWORD_LENGTH', 255);
define('MIN_USERNAME_LENGTH', 3);
define('MAX_USERNAME_LENGTH', 50);
define('MAX_EMAIL_LENGTH', 255);
define('MAX_TITLE_LENGTH', 255);
define('MAX_DESCRIPTION_LENGTH', 5000);
define('MAX_BIO_LENGTH', 500);
define('MAX_REASON_LENGTH', 500);

// Numeric limits
define('MIN_RATING', 1);
define('MAX_RATING', 5);
define('MIN_CAPACITY', 1);
define('MAX_CAPACITY', 1000);
define('MIN_DURATION', 15); // minutes
define('MAX_DURATION', 480); // 8 hours
define('MIN_FEE', 0);
define('MAX_FEE', 999999.99);

/**
 * ==========================================
 * DATE/TIME FORMATS
 * ==========================================
 */
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'F j, Y');
define('DISPLAY_TIME_FORMAT', 'g:i A');
define('DISPLAY_DATETIME_FORMAT', 'F j, Y \a\t g:i A');

/**
 * ==========================================
 * SESSION CONFIGURATION
 * ==========================================
 */
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('SESSION_COOKIE_LIFETIME', 86400 * 7); // 7 days in seconds
define('SESSION_NAME', 'SKILLSHARE_SESSION');

/**
 * ==========================================
 * RATING CONFIGURATION
 * ==========================================
 */
define('RATING_STARS', [1, 2, 3, 4, 5]);
define('LOW_RATING_THRESHOLD', 2.5);
define('GOOD_RATING_THRESHOLD', 3.5);
define('EXCELLENT_RATING_THRESHOLD', 4.5);

/**
 * ==========================================
 * CAPACITY WARNINGS
 * ==========================================
 */
define('LOW_CAPACITY_THRESHOLD', 3); // Show warning when ≤ 3 seats left
define('FULL_CAPACITY', 0); // Session is full

/**
 * ==========================================
 * DEFAULT VALUES
 * ==========================================
 */
define('DEFAULT_AVATAR', '/SkillShare-Local/public/assets/images/default-avatar.png');
define('DEFAULT_SESSION_IMAGE', '/SkillShare-Local/public/assets/images/default-session.jpg');
define('DEFAULT_THEME', 'light');
define('DEFAULT_LANGUAGE', 'en');
define('DEFAULT_TIMEZONE', 'UTC');
define('DEFAULT_CURRENCY', 'USD');

/**
 * ==========================================
 * IMPACT TRACKING
 * ==========================================
 */
define('IMPACT_UNIT_KG', 'kg CO2');
define('IMPACT_ACTIVE', true);
define('IMPACT_INACTIVE', false);

/**
 * ==========================================
 * ERROR MESSAGES
 * ==========================================
 */
define('ERROR_UNAUTHORIZED', 'You do not have permission to access this resource.');
define('ERROR_NOT_FOUND', 'The requested resource was not found.');
define('ERROR_INVALID_INPUT', 'Invalid input provided. Please check your data.');
define('ERROR_DATABASE', 'A database error occurred. Please try again later.');
define('ERROR_FILE_UPLOAD', 'File upload failed. Please try again.');
define('ERROR_SESSION_EXPIRED', 'Your session has expired. Please log in again.');
define('ERROR_GENERIC', 'An error occurred. Please try again.');

/**
 * ==========================================
 * SUCCESS MESSAGES
 * ==========================================
 */
define('SUCCESS_LOGIN', 'Login successful. Welcome back!');
define('SUCCESS_LOGOUT', 'You have been logged out successfully.');
define('SUCCESS_REGISTER', 'Registration successful. Please log in.');
define('SUCCESS_PROFILE_UPDATE', 'Your profile has been updated successfully.');
define('SUCCESS_SESSION_CREATE', 'Session created successfully.');
define('SUCCESS_SESSION_UPDATE', 'Session updated successfully.');
define('SUCCESS_SESSION_DELETE', 'Session deleted successfully.');
define('SUCCESS_BOOKING_CREATE', 'Booking created successfully.');
define('SUCCESS_BOOKING_UPDATE', 'Booking updated successfully.');
define('SUCCESS_RATING_SUBMIT', 'Rating submitted successfully.');

/**
 * ==========================================
 * CURRENCY SYMBOLS
 * ==========================================
 */
define('CURRENCY_SYMBOLS', [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'JPY' => '¥',
    'CAD' => 'CA$',
    'AUD' => 'A$',
    'CHF' => 'CHF',
    'CNY' => '¥',
    'INR' => '₹',
    'MXN' => 'MX$'
]);

/**
 * ==========================================
 * SUPPORTED LANGUAGES
 * ==========================================
 */
define('SUPPORTED_LANGUAGES', [
    'en' => 'English',
    'es' => 'Español',
    'fr' => 'Français',
    'de' => 'Deutsch',
    'it' => 'Italiano',
    'pt' => 'Português',
    'zh' => '中文',
    'ja' => '日本語',
    'ar' => 'العربية'
]);

/**
 * ==========================================
 * SUPPORTED TIMEZONES (Common ones)
 * ==========================================
 */
define('COMMON_TIMEZONES', [
    'UTC' => 'UTC',
    'America/New_York' => 'Eastern Time (US)',
    'America/Chicago' => 'Central Time (US)',
    'America/Denver' => 'Mountain Time (US)',
    'America/Los_Angeles' => 'Pacific Time (US)',
    'Europe/London' => 'London',
    'Europe/Paris' => 'Paris',
    'Europe/Berlin' => 'Berlin',
    'Asia/Tokyo' => 'Tokyo',
    'Asia/Shanghai' => 'Shanghai',
    'Asia/Dubai' => 'Dubai',
    'Australia/Sydney' => 'Sydney'
]);

/**
 * ==========================================
 * HELPER FUNCTIONS FOR CONSTANTS
 * ==========================================
 */

/**
 * Check if a role is valid
 * 
 * @param string $role Role to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_role($role) {
    return in_array($role, VALID_ROLES, true);
}

/**
 * Check if a session status is valid
 * 
 * @param string $status Status to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_session_status($status) {
    return in_array($status, VALID_SESSION_STATUSES, true);
}

/**
 * Check if a booking status is valid
 * 
 * @param string $status Status to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_booking_status($status) {
    return in_array($status, VALID_BOOKING_STATUSES, true);
}

/**
 * Check if a location type is valid
 * 
 * @param string $type Location type to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_location_type($type) {
    return in_array($type, VALID_LOCATION_TYPES, true);
}

/**
 * Check if a fee type is valid
 * 
 * @param string $type Fee type to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_fee_type($type) {
    return in_array($type, VALID_FEE_TYPES, true);
}

/**
 * Get currency symbol
 * 
 * @param string $currency Currency code (e.g., 'USD')
 * @return string Currency symbol or code if not found
 */
function get_currency_symbol($currency) {
    return CURRENCY_SYMBOLS[$currency] ?? $currency;
}

/**
 * Check if user is admin
 * 
 * @param string $role User role
 * @return bool True if admin, false otherwise
 */
function is_admin($role) {
    return $role === ROLE_ADMIN;
}

/**
 * Check if user is instructor
 * 
 * @param string $role User role
 * @return bool True if instructor, false otherwise
 */
function is_instructor($role) {
    return $role === ROLE_INSTRUCTOR;
}

/**
 * Check if user is learner
 * 
 * @param string $role User role
 * @return bool True if learner, false otherwise
 */
function is_learner($role) {
    return $role === ROLE_LEARNER;
}
