<?php
/**
 * AJAX Endpoint - Save User Preference
 * Handles saving user preferences via AJAX to database
 */

session_start();

// Include configuration and models
require_once '../../app/config/database.php';
require_once '../../app/models/UserSettings.php';

// Set JSON header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Require authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['key']) || !isset($data['value'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$key = $data['key'];
$value = $data['value'];

try {
    // Save to database using UserSettings model
    $result = UserSettings::updateSingle($userId, $key, $value);
    
    if ($result) {
        // Determine if page reload needed based on preference type
        $reloadRequired = in_array($key, ['theme', 'language']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Preference saved successfully',
            'reload' => $reloadRequired,
            'key' => $key,
            'value' => $value
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save preference. Invalid setting key.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving preference: ' . $e->getMessage()
    ]);
}
