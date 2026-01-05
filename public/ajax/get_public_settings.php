<?php
/**
 * Get Public Settings API
 * 
 * Returns only public settings that are safe to expose to frontend
 * Used by JavaScript to access app configuration
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../app/config/database.php';
require_once '../../app/models/Settings.php';

try {
    // Get only public settings
    $settings = Settings::getPublic();
    
    // Return as JSON
    echo json_encode([
        'success' => true,
        'settings' => $settings,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load settings',
        'message' => APP_DEBUG ? $e->getMessage() : 'Internal server error'
    ]);
}
