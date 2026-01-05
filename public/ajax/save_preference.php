<?php
/**
 * AJAX Endpoint - Save User Preference
 * Handles saving user preferences via AJAX
 */

// Include configuration and cookie functions
require_once '../../app/config/config.php';

// Set JSON header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['name']) || !isset($data['value'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$name = $data['name'];
$value = $data['value'];

// Set preference cookie
$result = setPreferenceCookie($name, $value);

if ($result) {
    // Determine if page reload needed based on preference type
    $reloadRequired = in_array($name, ['theme', 'language']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Preference saved successfully',
        'reload' => $reloadRequired
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save preference. Invalid preference name or value.'
    ]);
}
