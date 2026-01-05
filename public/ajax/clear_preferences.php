<?php
/**
 * AJAX Endpoint - Clear All Preferences
 */

require_once '../../app/config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$result = deleteAllPreferenceCookies();

echo json_encode([
    'success' => $result,
    'message' => $result ? 'Preferences cleared successfully' : 'Failed to clear preferences'
]);
