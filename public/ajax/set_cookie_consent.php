<?php
/**
 * AJAX Endpoint - Set Cookie Consent
 * Handles cookie consent acceptance/decline via AJAX
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
if (!isset($data['accepted'])) {
    echo json_encode(['success' => false, 'message' => 'Missing accepted parameter']);
    exit;
}

$accepted = (bool)$data['accepted'];

// Set cookie consent
$result = setCookieConsent($accepted);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => $accepted ? 'Cookies accepted' : 'Cookies declined',
        'accepted' => $accepted
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to set cookie consent'
    ]);
}
