<?php
/**
 * AJAX Endpoint - Save Multiple Preferences
 */

require_once '../../app/config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['preferences']) || !is_array($data['preferences'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid preferences data']);
    exit;
}

$result = setPreferences($data['preferences']);

echo json_encode([
    'success' => $result,
    'message' => $result ? 'Preferences saved successfully' : 'Failed to save preferences'
]);
