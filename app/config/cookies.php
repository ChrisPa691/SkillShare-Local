<?php
/**
 * Cookie Management Configuration and Functions
 * 
 * This file centralizes all cookie-related functionality for the SkillShare platform.
 * It provides functions for creating, reading, and deleting cookies with proper security settings.
 * 
 * 
 * @package SkillShare
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ALLOW_INCLUDE')) {
    die('Direct access not permitted');
}

// ============================================================================
// 🔧 COOKIE CONFIGURATION CONSTANTS
// ============================================================================

// General cookie configuration
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIE_SECURE', false); // Set to true when using HTTPS in production
define('COOKIE_HTTPONLY', true);
define('COOKIE_SAMESITE', 'Lax'); // 'Strict', 'Lax', or 'None'

// Authentication cookie settings
define('AUTH_COOKIE_NAME', 'skillshare_auth');
define('AUTH_COOKIE_EXPIRY', 86400); // 24 hours (1 day)

// Preference cookie settings
define('PREF_COOKIE_NAME', 'skillshare_prefs');
define('PREF_COOKIE_EXPIRY', 2592000); // 30 days

// Consent cookie settings
define('CONSENT_COOKIE_NAME', 'skillshare_consent');
define('CONSENT_COOKIE_EXPIRY', 31536000); // 1 year

// Tracking cookie settings
define('TRACKING_COOKIE_NAME', 'skillshare_visitor');
define('TRACKING_COOKIE_EXPIRY', 2592000); // 30 days


// ============================================================================
// 🔐 AUTHENTICATION COOKIE FUNCTIONS
// ============================================================================

/**
 * TODO: Create authentication cookie after successful login
 * 
 * Function name: createAuthCookie()
 * 
 * Parameters:
 * @param string $sessionId - The unique session identifier (NOT user data!)
 * @param int $expiry - Cookie expiration time (default: AUTH_COOKIE_EXPIRY)
 * @return bool - True if cookie was set successfully, false otherwise
 * 
 * Requirements:
 * - Store ONLY session identifier (never password, email, or sensitive data)
 * - Use setcookie() with proper security flags
 * - Set expiry time (current time + AUTH_COOKIE_EXPIRY)
 * - Apply COOKIE_PATH, COOKIE_HTTPONLY, COOKIE_SECURE
 * - Must be called BEFORE any HTML output (headers not sent)
 * 
 * Security notes:
 * - Use session_id() to get current session identifier
 * - Never store plaintext passwords
 * - Never store personal information (name, email, role)
 * - HttpOnly flag prevents JavaScript access (XSS protection)
 * - Secure flag ensures HTTPS transmission (when available)
 * 
 * Example usage:
 * createAuthCookie(session_id());
 * 
 * Example implementation:
 * setcookie(
 *     AUTH_COOKIE_NAME,
 *     $sessionId,
 *     time() + $expiry,
 *     COOKIE_PATH,
 *     COOKIE_DOMAIN,
 *     COOKIE_SECURE,
 *     COOKIE_HTTPONLY
 * );
 */
function createAuthCookie($sessionId = null, $expiry = AUTH_COOKIE_EXPIRY) {
    // Use current session ID if not provided
    if ($sessionId === null) {
        $sessionId = session_id();
    }
    
    // Validate session ID
    if (empty($sessionId)) {
        return false;
    }
    
    // Set cookie with security flags
    $options = [
        'expires' => time() + $expiry,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    return setcookie(AUTH_COOKIE_NAME, $sessionId, $options);
}


/**
 * TODO: Validate authentication cookie
 * 
 * Function name: validateAuthCookie()
 * 
 * Parameters: None
 * @return bool - True if valid cookie exists, false otherwise
 * 
 * Requirements:
 * - Check if AUTH_COOKIE_NAME exists in $_COOKIE
 * - Validate cookie value matches current session_id()
 * - Return false if cookie missing or invalid
 * - Return true if cookie valid
 * 
 * Usage:
 * - Call from auth_guard.php to protect pages
 * - Redirect to login if returns false
 * 
 * Example usage:
 * if (!validateAuthCookie()) {
 *     header('Location: login.php');
 *     exit;
 * }
 */
function validateAuthCookie() {
    // Check if auth cookie exists
    if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
        return false;
    }
    
    // Get cookie value
    $cookieSessionId = $_COOKIE[AUTH_COOKIE_NAME];
    
    // Validate cookie value format
    if (!validateCookieValue($cookieSessionId)) {
        return false;
    }
    
    // Validate against current session ID
    $currentSessionId = session_id();
    
    // If no session started, try to resume with cookie
    if (empty($currentSessionId)) {
        session_id($cookieSessionId);
        session_start();
        return isset($_SESSION['user_id']);
    }
    
    // Check if cookie matches current session
    return $cookieSessionId === $currentSessionId;
}


/**
 * TODO: Get authentication cookie value
 * 
 * Function name: getAuthCookie()
 * 
 * Parameters: None
 * @return string|null - Cookie value if exists, null otherwise
 * 
 * Requirements:
 * - Return cookie value if AUTH_COOKIE_NAME exists
 * - Return null if cookie doesn't exist
 * - Use isset() to check before accessing $_COOKIE
 * 
 * Example usage:
 * $sessionId = getAuthCookie();
 */
function getAuthCookie() {
    return isset($_COOKIE[AUTH_COOKIE_NAME]) ? $_COOKIE[AUTH_COOKIE_NAME] : null;
}


// ============================================================================
// 🗑️ COOKIE DELETION FUNCTIONS (Logout)
// ============================================================================

/**
 * TODO: Delete authentication cookie on logout
 * 
 * Function name: deleteAuthCookie()
 * 
 * Parameters: None
 * @return bool - True if cookie was deleted, false otherwise
 * 
 * Requirements:
 * - Set cookie with same name but expiry in the past (time() - 3600)
 * - Use same path, domain, and flags as when created
 * - Unset from $_COOKIE array
 * - Must be called BEFORE any HTML output
 * 
 * Example usage:
 * deleteAuthCookie();
 * 
 * Example implementation:
 * setcookie(
 *     AUTH_COOKIE_NAME,
 *     '',
 *     time() - 3600,
 *     COOKIE_PATH,
 *     COOKIE_DOMAIN,
 *     COOKIE_SECURE,
 *     COOKIE_HTTPONLY
 * );
 * unset($_COOKIE[AUTH_COOKIE_NAME]);
 */
function deleteAuthCookie() {
    if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
        return true; // Already deleted
    }
    
    // Set cookie with past expiration
    $options = [
        'expires' => time() - 3600,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    $result = setcookie(AUTH_COOKIE_NAME, '', $options);
    
    // Unset from current request
    unset($_COOKIE[AUTH_COOKIE_NAME]);
    
    return $result;
}


/**
 * TODO: Delete all preference cookies
 * 
 * Function name: deleteAllPreferenceCookies()
 * 
 * Parameters: None
 * @return bool - True if cookies were deleted, false otherwise
 * 
 * Requirements:
 * - Delete PREF_COOKIE_NAME
 * - Use same technique as deleteAuthCookie()
 * - Iterate through any custom preference cookies
 * - Unset from $_COOKIE array
 * 
 * Usage:
 * - Called when user wants to reset preferences
 * - Can be called on logout (optional)
 */
function deleteAllPreferenceCookies() {
    $options = [
        'expires' => time() - 3600,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    // Delete main preferences cookie
    if (isset($_COOKIE[PREF_COOKIE_NAME])) {
        setcookie(PREF_COOKIE_NAME, '', $options);
        unset($_COOKIE[PREF_COOKIE_NAME]);
    }
    
    // Delete individual preference cookies (if any)
    $prefixPattern = 'skillshare_pref_';
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, $prefixPattern) === 0) {
            setcookie($name, '', $options);
            unset($_COOKIE[$name]);
        }
    }
    
    return true;
}


/**
 * TODO: Delete all cookies (complete cleanup)
 * 
 * Function name: deleteAllCookies()
 * 
 * Parameters: None
 * @return bool - True if all cookies deleted
 * 
 * Requirements:
 * - Call deleteAuthCookie()
 * - Call deleteAllPreferenceCookies()
 * - Delete any other application cookies
 * 
 * Usage:
 * - Full logout cleanup
 * - User account deletion
 * - Security breach response
 */
function deleteAllCookies() {
    // Delete authentication cookie
    deleteAuthCookie();
    
    // Delete preference cookies
    deleteAllPreferenceCookies();
    
    // Delete consent cookie (optional - keep for GDPR compliance)
    // Uncomment if you want to clear consent on full logout
    /*
    if (isset($_COOKIE[CONSENT_COOKIE_NAME])) {
        $options = [
            'expires' => time() - 3600,
            'path' => COOKIE_PATH,
            'domain' => COOKIE_DOMAIN,
            'secure' => COOKIE_SECURE,
            'httponly' => COOKIE_HTTPONLY,
            'samesite' => COOKIE_SAMESITE
        ];
        setcookie(CONSENT_COOKIE_NAME, '', $options);
        unset($_COOKIE[CONSENT_COOKIE_NAME]);
    }
    */
    
    // Delete tracking cookie
    if (isset($_COOKIE[TRACKING_COOKIE_NAME])) {
        $options = [
            'expires' => time() - 3600,
            'path' => COOKIE_PATH,
            'domain' => COOKIE_DOMAIN,
            'secure' => COOKIE_SECURE,
            'httponly' => COOKIE_HTTPONLY,
            'samesite' => COOKIE_SAMESITE
        ];
        setcookie(TRACKING_COOKIE_NAME, '', $options);
        unset($_COOKIE[TRACKING_COOKIE_NAME]);
    }
    
    return true;
}


// ============================================================================
// ⚙️ USER PREFERENCE COOKIE FUNCTIONS
// ============================================================================

/**
 * TODO: Set user preference cookie
 * 
 * Function name: setPreferenceCookie()
 * 
 * Parameters:
 * @param string $name - Preference name (e.g., 'location', 'theme', 'language')
 * @param string $value - Preference value
 * @param int $expiry - Cookie expiration time (default: PREF_COOKIE_EXPIRY)
 * @return bool - True if cookie was set, false otherwise
 * 
 * Requirements:
 * - Store non-sensitive preferences only
 * - Use JSON encoding for complex values
 * - Set longer expiry than auth cookies (30 days)
 * - Use same security flags (HttpOnly, Secure)
 * 
 * Allowed preferences:
 * - Preferred location (city or "online")
 * - Selected category filter
 * - Language choice
 * - Theme (light/dark mode)
 * - Results per page
 * - Sort order preference
 * 
 * ⚠️ Never store:
 * - Passwords
 * - Email addresses
 * - Phone numbers
 * - Payment information
 * - Personal identification data
 * 
 * Example usage:
 * setPreferenceCookie('location', 'Riyadh');
 * setPreferenceCookie('theme', 'dark');
 * setPreferenceCookie('language', 'ar');
 */
function setPreferenceCookie($name, $value, $expiry = PREF_COOKIE_EXPIRY) {
    // Validate preference name
    $allowedPreferences = ['location', 'category', 'language', 'theme', 'results_per_page', 'sort_order'];
    if (!in_array($name, $allowedPreferences)) {
        return false;
    }
    
    // Get existing preferences
    $preferences = getAllPreferences();
    
    // Update preference
    $preferences[$name] = $value;
    
    // Store as JSON
    $jsonValue = json_encode($preferences);
    
    // Set cookie
    $options = [
        'expires' => time() + $expiry,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    return setcookie(PREF_COOKIE_NAME, $jsonValue, $options);
}


/**
 * TODO: Get user preference from cookie
 * 
 * Function name: getPreferenceCookie()
 * 
 * Parameters:
 * @param string $name - Preference name
 * @param mixed $default - Default value if preference not set
 * @return mixed - Preference value or default
 * 
 * Requirements:
 * - Check if preference cookie exists
 * - Decode JSON if needed
 * - Return default value if not found
 * - Validate returned data
 * 
 * Example usage:
 * $location = getPreferenceCookie('location', 'All Locations');
 * $theme = getPreferenceCookie('theme', 'light');
 */
function getPreferenceCookie($name, $default = null) {
    // Get all preferences
    $preferences = getAllPreferences();
    
    // Return specific preference or default
    return isset($preferences[$name]) ? $preferences[$name] : $default;
}


/**
 * TODO: Set multiple preferences at once
 * 
 * Function name: setPreferences()
 * 
 * Parameters:
 * @param array $preferences - Associative array of preferences
 * @param int $expiry - Cookie expiration time (default: PREF_COOKIE_EXPIRY)
 * @return bool - True if all preferences set successfully
 * 
 * Requirements:
 * - Accept associative array
 * - Store as JSON in single cookie OR multiple cookies
 * - Validate each preference
 * - Return false if any preference invalid
 * 
 * Example usage:
 * setPreferences([
 *     'location' => 'Riyadh',
 *     'theme' => 'dark',
 *     'language' => 'ar'
 * ]);
 */
function setPreferences($preferences, $expiry = PREF_COOKIE_EXPIRY) {
    // Validate input
    if (!is_array($preferences)) {
        return false;
    }
    
    // Validate each preference name
    $allowedPreferences = ['location', 'category', 'language', 'theme', 'results_per_page', 'sort_order'];
    foreach ($preferences as $name => $value) {
        if (!in_array($name, $allowedPreferences)) {
            return false;
        }
    }
    
    // Get existing preferences and merge
    $existing = getAllPreferences();
    $merged = array_merge($existing, $preferences);
    
    // Store as JSON
    $jsonValue = json_encode($merged);
    
    // Set cookie
    $options = [
        'expires' => time() + $expiry,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    return setcookie(PREF_COOKIE_NAME, $jsonValue, $options);
}


/**
 * TODO: Get all user preferences
 * 
 * Function name: getAllPreferences()
 * 
 * Parameters: None
 * @return array - Associative array of all preferences
 * 
 * Requirements:
 * - Return all stored preferences
 * - Decode JSON data
 * - Return empty array if no preferences
 * - Include default values for missing preferences
 * 
 * Example usage:
 * $prefs = getAllPreferences();
 * echo $prefs['theme']; // 'dark'
 */
function getAllPreferences() {
    // Default preferences
    $defaults = [
        'location' => 'All Locations',
        'category' => 'all',
        'language' => 'en',
        'theme' => 'light',
        'results_per_page' => 12,
        'sort_order' => 'date_desc'
    ];
    
    // Check if preferences cookie exists
    if (!isset($_COOKIE[PREF_COOKIE_NAME])) {
        return $defaults;
    }
    
    // Decode JSON
    $preferences = json_decode($_COOKIE[PREF_COOKIE_NAME], true);
    
    // Validate decoded data
    if (!is_array($preferences)) {
        return $defaults;
    }
    
    // Merge with defaults (preferences override defaults)
    return array_merge($defaults, $preferences);
}


// ============================================================================
// 🍪 COOKIE CONSENT FUNCTIONS (GDPR Compliance)
// ============================================================================

/**
 * TODO: Set cookie consent status
 * 
 * Function name: setCookieConsent()
 * 
 * Parameters:
 * @param bool $accepted - Whether user accepted cookies
 * @return bool - True if consent cookie was set
 * 
 * Requirements:
 * - Store user's consent decision
 * - Set long expiry (1 year)
 * - Value: 'accepted' or 'declined'
 * - Use to control analytics/tracking cookies
 * 
 * Example usage:
 * setCookieConsent(true); // User accepted
 * setCookieConsent(false); // User declined
 */
function setCookieConsent($accepted) {
    // Convert boolean to string value
    $value = $accepted ? 'accepted' : 'declined';
    
    // Set cookie with long expiry
    $options = [
        'expires' => time() + CONSENT_COOKIE_EXPIRY,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => false, // Allow JavaScript to read for banner control
        'samesite' => COOKIE_SAMESITE
    ];
    
    $result = setcookie(CONSENT_COOKIE_NAME, $value, $options);
    
    // If user accepted, set tracking cookie
    if ($accepted && $result) {
        setVisitorTrackingCookie();
    }
    
    return $result;
}


/**
 * TODO: Check if user has given cookie consent
 * 
 * Function name: hasCookieConsent()
 * 
 * Parameters: None
 * @return bool|null - True if accepted, false if declined, null if not set
 * 
 * Requirements:
 * - Check if CONSENT_COOKIE_NAME exists
 * - Return null if no consent given yet (show banner)
 * - Return true if accepted
 * - Return false if declined
 * 
 * Example usage:
 * if (hasCookieConsent() === null) {
 *     // Show consent banner
 * }
 */
function hasCookieConsent() {
    // Check if consent cookie exists
    if (!isset($_COOKIE[CONSENT_COOKIE_NAME])) {
        return null; // No consent given yet
    }
    
    $value = $_COOKIE[CONSENT_COOKIE_NAME];
    
    // Return true if accepted, false if declined
    if ($value === 'accepted') {
        return true;
    } elseif ($value === 'declined') {
        return false;
    }
    
    // Invalid value
    return null;
}


/**
 * TODO: Check if consent banner should be shown
 * 
 * Function name: shouldShowConsentBanner()
 * 
 * Parameters: None
 * @return bool - True if banner should be shown
 * 
 * Requirements:
 * - Return true if no consent cookie exists
 * - Return false if consent already given (accepted or declined)
 * 
 * Example usage:
 * if (shouldShowConsentBanner()) {
 *     // Display consent banner in header.php
 * }
 */
function shouldShowConsentBanner() {
    // Show banner if no consent has been given
    return hasCookieConsent() === null;
}


// ============================================================================
// 📊 TRACKING & ANALYTICS COOKIE FUNCTIONS
// ============================================================================

/**
 * TODO: Set visitor tracking cookie
 * 
 * Function name: setVisitorTrackingCookie()
 * 
 * Parameters: None
 * @return bool - True if tracking cookie was set
 * 
 * Requirements:
 * - Generate unique visitor ID (use uniqid() or random_bytes())
 * - Store only if user has given consent
 * - Anonymous tracking only (no personal data)
 * - Use for:
 *   - Unique visit counts
 *   - Preventing duplicate ratings
 *   - Session tracking
 * 
 * ⚠️ Privacy requirements:
 * - No personal profiling
 * - No cross-site tracking
 * - Anonymous identifiers only
 * - Respect user consent
 * 
 * Example usage:
 * if (hasCookieConsent() === true) {
 *     setVisitorTrackingCookie();
 * }
 */
function setVisitorTrackingCookie() {
    // Only set if user has consented OR if consent not required
    $consent = hasCookieConsent();
    if ($consent === false) {
        return false; // User declined
    }
    
    // Check if tracking cookie already exists
    if (isset($_COOKIE[TRACKING_COOKIE_NAME])) {
        return true; // Already set
    }
    
    // Generate unique anonymous visitor ID
    $visitorId = bin2hex(random_bytes(16)); // 32-character hex string
    
    // Set cookie
    $options = [
        'expires' => time() + TRACKING_COOKIE_EXPIRY,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => true, // Prevent JavaScript access for security
        'samesite' => COOKIE_SAMESITE
    ];
    
    return setcookie(TRACKING_COOKIE_NAME, $visitorId, $options);
}


/**
 * TODO: Get visitor tracking ID
 * 
 * Function name: getVisitorTrackingId()
 * 
 * Parameters: None
 * @return string|null - Visitor ID or null if not set
 * 
 * Requirements:
 * - Return tracking ID from cookie
 * - Return null if cookie doesn't exist
 * - Use for duplicate prevention
 * 
 * Example usage:
 * $visitorId = getVisitorTrackingId();
 * if ($visitorId && hasUserRated($sessionId, $visitorId)) {
 *     // Prevent duplicate rating
 * }
 */
function getVisitorTrackingId() {
    return isset($_COOKIE[TRACKING_COOKIE_NAME]) ? $_COOKIE[TRACKING_COOKIE_NAME] : null;
}


// ============================================================================
// 🛠️ HELPER FUNCTIONS
// ============================================================================

/**
 * TODO: Check if cookies are enabled in browser
 * 
 * Function name: areCookiesEnabled()
 * 
 * Parameters: None
 * @return bool - True if cookies are enabled
 * 
 * Requirements:
 * - Try to set a test cookie
 * - Check if cookie was set
 * - Delete test cookie
 * - Return true/false based on result
 * 
 * Usage:
 * - Display warning if cookies disabled
 * - Fallback to URL-based sessions
 */
function areCookiesEnabled() {
    // Check if we can read existing cookies
    if (!empty($_COOKIE)) {
        return true;
    }
    
    // Try to set a test cookie
    $testName = 'test_cookie_' . time();
    $testValue = 'test';
    
    $options = [
        'expires' => time() + 60, // 1 minute
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    setcookie($testName, $testValue, $options);
    
    // Check on next request (can't verify immediately in same request)
    // This function should be called on subsequent page loads
    if (isset($_COOKIE[$testName]) && $_COOKIE[$testName] === $testValue) {
        // Delete test cookie
        $options['expires'] = time() - 3600;
        setcookie($testName, '', $options);
        return true;
    }
    
    return false;
}


/**
 * TODO: Validate cookie value against security rules
 * 
 * Function name: validateCookieValue()
 * 
 * Parameters:
 * @param string $value - Cookie value to validate
 * @return bool - True if valid, false otherwise
 * 
 * Requirements:
 * - Check for SQL injection patterns
 * - Check for XSS patterns
 * - Validate format (alphanumeric for session IDs)
 * - Check length limits
 * - Return false if suspicious
 * 
 * Example usage:
 * if (!validateCookieValue($_COOKIE['some_cookie'])) {
 *     // Invalid/suspicious cookie - delete it
 * }
 */
function validateCookieValue($value) {
    // Check if value is empty
    if (empty($value)) {
        return false;
    }
    
    // Check length (prevent excessively long values)
    if (strlen($value) > 4096) { // Standard cookie size limit
        return false;
    }
    
    // Check for null bytes (security risk)
    if (strpos($value, "\0") !== false) {
        return false;
    }
    
    // For session IDs, validate alphanumeric format
    // PHP session IDs are typically alphanumeric with hyphens
    if (preg_match('/^[a-zA-Z0-9,-]+$/', $value)) {
        return true;
    }
    
    // For JSON values (preferences), validate JSON format
    if ($value[0] === '{' || $value[0] === '[') {
        $decoded = json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    // For simple string values (accepted/declined), validate allowed values
    $allowedValues = ['accepted', 'declined', 'light', 'dark', 'en', 'ar'];
    if (in_array($value, $allowedValues)) {
        return true;
    }
    
    // Check for SQL injection patterns (basic)
    $sqlPatterns = [
        '/\bSELECT\b/i',
        '/\bUNION\b/i',
        '/\bINSERT\b/i',
        '/\bUPDATE\b/i',
        '/\bDELETE\b/i',
        '/\bDROP\b/i',
        '/[\'";]/', // Quotes and semicolons
    ];
    
    foreach ($sqlPatterns as $pattern) {
        if (preg_match($pattern, $value)) {
            return false;
        }
    }
    
    // Check for XSS patterns (basic)
    $xssPatterns = [
        '/<script/i',
        '/<iframe/i',
        '/javascript:/i',
        '/onerror=/i',
        '/onload=/i',
    ];
    
    foreach ($xssPatterns as $pattern) {
        if (preg_match($pattern, $value)) {
            return false;
        }
    }
    
    // If none of the validation rules match, consider it potentially unsafe
    // Allow only alphanumeric, spaces, and common safe characters
    return preg_match('/^[a-zA-Z0-9\s\-_.,@]+$/', $value);
}


/**
 * TODO: Handle expired cookies gracefully
 * 
 * Function name: handleExpiredCookie()
 * 
 * Parameters:
 * @param string $cookieName - Name of the expired cookie
 * @return void
 * 
 * Requirements:
 * - Delete expired cookie
 * - Log event (optional)
 * - Redirect to login if auth cookie
 * - Show message to user
 * 
 * Usage:
 * - Called when cookie validation fails
 * - Automatic cleanup
 */
function handleExpiredCookie($cookieName) {
    // Delete the expired cookie
    $options = [
        'expires' => time() - 3600,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    setcookie($cookieName, '', $options);
    unset($_COOKIE[$cookieName]);
    
    // If it's an auth cookie, redirect to login
    if ($cookieName === AUTH_COOKIE_NAME) {
        // Destroy session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Set error message (if session available)
        if (!headers_sent()) {
            session_start();
            $_SESSION['error'] = 'Your session has expired. Please log in again.';
            session_write_close();
            
            // Redirect to login
            header('Location: ' . COOKIE_PATH . 'login.php');
            exit;
        }
    }
    
    // Log the event (optional - would need logging system)
    // error_log("Expired cookie handled: {$cookieName}");
}


// ============================================================================
// 🎯 INTEGRATION NOTES FOR IMPLEMENTATION
// ============================================================================

/**
 * FILES TO MODIFY WHEN IMPLEMENTING COOKIES:
 * 
 * 1. login.php (public/login.php)
 *    - After successful authentication, call createAuthCookie(session_id())
 *    - Set preference cookies for "Remember location" checkbox
 * 
 * 2. auth_guard.php (app/includes/auth_guard.php)
 *    - Add validateAuthCookie() check
 *    - Redirect to login if validation fails
 *    - Extend session if cookie valid
 * 
 * 3. logout.php (public/logout.php)
 *    - Call deleteAuthCookie() before session_destroy()
 *    - Optionally call deleteAllCookies() for complete cleanup
 * 
 * 4. config.php (app/config/config.php)
 *    - Include cookies.php
 *    - Set ALLOW_INCLUDE constant before including
 * 
 * 5. header.php (app/includes/header.php)
 *    - Check shouldShowConsentBanner()
 *    - Display consent banner if needed
 *    - Apply theme/language preferences from cookies
 * 
 * 6. main.js (public/assets/js/main.js)
 *    - Handle consent banner interactions
 *    - AJAX call to set consent cookie
 *    - Hide/show banner based on response
 * 
 * 7. All dashboard files
 *    - Read preference cookies
 *    - Auto-apply filters from preferences
 *    - Provide UI to change preferences
 * 
 * TESTING CHECKLIST:
 * ☐ Test cookie creation on login
 * ☐ Test cookie validation on protected pages
 * ☐ Test cookie deletion on logout
 * ☐ Test preference persistence across sessions
 * ☐ Test consent banner appears/hides correctly
 * ☐ Test security flags (HttpOnly, Secure)
 * ☐ Test cookie expiration handling
 * ☐ Test with cookies disabled in browser
 * ☐ Test multiple browsers/devices
 * ☐ Test GDPR compliance scenarios
 */

// ============================================================================
// END OF FILE
// ============================================================================
