<?php
/**
 * Booking Management Handler
 * Handles accept/reject actions for booking requests (Instructor only)
 */

session_start();

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/includes/helpers.php';
require_once __DIR__ . '/../app/includes/auth_guard.php';

// Require instructor login
require_login();
require_role('instructor');

// Get parameters
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Validate parameters
if ($booking_id <= 0 || !in_array($action, ['accept', 'reject'])) {
    set_flash('error', 'Invalid request.');
    redirect('dashboard.php');
    exit;
}

// Get booking details
$booking = Booking::getBookingById($booking_id);

if (!$booking) {
    set_flash('error', 'Booking not found.');
    redirect('dashboard.php');
    exit;
}

// Get session details to verify instructor ownership
$session = Session::getSessionById($booking['session_id']);

if (!$session || $session['instructor_id'] != $_SESSION['user_id']) {
    set_flash('error', 'You do not have permission to manage this booking.');
    redirect('dashboard.php');
    exit;
}

// Check if booking is still pending
if ($booking['status'] !== 'pending') {
    set_flash('warning', 'This booking has already been processed.');
    redirect('dashboard.php');
    exit;
}

// Process the action
if ($action === 'accept') {
    // Check if session has capacity
    if ($session['capacity_remaining'] <= 0) {
        set_flash('error', 'This session is already full.');
        redirect('dashboard.php');
        exit;
    }
    
    $result = Booking::acceptBooking($booking_id);
    
    if ($result) {
        // Update session capacity
        $new_capacity = $session['capacity_remaining'] - 1;
        Session::updateSession($session['session_id'], ['capacity_remaining' => $new_capacity]);
        
        set_flash('success', 'Booking accepted successfully!');
    } else {
        set_flash('error', 'Failed to accept booking. Please try again.');
    }
} elseif ($action === 'reject') {
    $result = Booking::updateBookingStatus($booking_id, 'declined');
    
    if ($result) {
        set_flash('success', 'Booking declined.');
    } else {
        set_flash('error', 'Failed to decline booking. Please try again.');
    }
}

// Redirect back to dashboard
redirect('dashboard.php');
