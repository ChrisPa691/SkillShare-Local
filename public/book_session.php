<?php
/**
 * Book Session Handler
 * Handles booking creation for learners
 */

session_start();

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/includes/helpers.php';
require_once __DIR__ . '/../app/includes/auth_guard.php';

// Require learner login
require_login();
require_role('learner');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate session_id
    $session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : 0;
    
    if ($session_id <= 0) {
        set_flash('error', 'Invalid session ID.');
        redirect('sessions.php');
        exit;
    }
    
    // Get session details
    $session = Session::getSessionById($session_id);
    
    if (!$session) {
        set_flash('error', 'Session not found.');
        redirect('sessions.php');
        exit;
    }
    
    // Validate session is available for booking
    if ($session['status'] !== 'upcoming') {
        set_flash('error', 'This session is not available for booking.');
        redirect('session_view.php?id=' . $session_id);
        exit;
    }
    
    if ($session['capacity_remaining'] <= 0) {
        set_flash('error', 'This session is full.');
        redirect('session_view.php?id=' . $session_id);
        exit;
    }
    
    // Check if user already booked this session
    $existing_booking = Booking::getBookingByLearnerAndSession($_SESSION['user_id'], $session_id);
    
    if ($existing_booking) {
        set_flash('warning', 'You have already booked this session.');
        redirect('session_view.php?id=' . $session_id);
        exit;
    }
    
    // Create booking
    $booking_data = [
        'session_id' => $session_id,
        'learner_id' => $_SESSION['user_id']
    ];
    
    $booking_id = Booking::createBooking($booking_data);
    
    if ($booking_id) {
        set_flash('success', 'Booking request submitted successfully! The instructor will review your request.');
        redirect('my_bookings.php');
    } else {
        set_flash('error', 'Failed to create booking. Please try again.');
        redirect('session_view.php?id=' . $session_id);
    }
    
} else {
    // If not POST, redirect to sessions
    redirect('sessions.php');
}
