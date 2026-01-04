<?php
/**
 * Logout Script
 * Destroys session and redirects to login page
 */

session_start();
require_once '../app/controllers/AuthController.php';

$result = AuthController::handleLogout();
header("Location: " . $result['redirect']);
exit();
?>
