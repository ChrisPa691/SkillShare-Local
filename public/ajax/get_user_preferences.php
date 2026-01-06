<?php
/**
 * Get User Preferences API
 * 
 * Returns user-specific preference settings from database
 * Used by JavaScript to sync localStorage with database values
 */

session_start();

header('Content-Type: application/json');

require_once '../../app/config/database.php';
require_once '../../app/models/UserSettings.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Authentication required'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Get user preferences
    $userSettings = UserSettings::get($userId);
    
    if (!$userSettings) {
        UserSettings::create($userId);
        $userSettings = UserSettings::get($userId);
    }
    
    $preferences = [
        'theme' => $userSettings['theme'] ?? 'light',
        'language' => $userSettings['language'] ?? 'en',
        'timezone' => $userSettings['timezone'] ?? 'UTC',
        'currency' => $userSettings['currency'] ?? 'GBP',
        'font_size' => $userSettings['font_size'] ?? 16,
        'contrast_mode' => $userSettings['contrast_mode'] ?? 'normal',
        'notify_email' => $userSettings['notify_email'] ?? true,
        'notify_inapp' => $userSettings['notify_inapp'] ?? true,
        'notify_push' => $userSettings['notify_push'] ?? false
    ];
    
    // Return as JSON
    echo json_encode([
        'success' => true,
        'preferences' => $preferences,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load preferences',
        'message' => $e->getMessage()
    ]);
}
