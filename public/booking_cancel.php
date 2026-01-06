<?php
/**
 * Booking Cancellation Handler
 * Allows learners to cancel their bookings
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
    
    // Validate booking_id
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    
    if ($booking_id <= 0) {
        set_flash('Invalid booking ID.', 'danger');
        redirect('my_bookings.php');
        exit;
    }
    
    // Get booking details
    $booking = Booking::getBookingById($booking_id);
    
    if (!$booking) {
        set_flash('Booking not found.', 'danger');
        redirect('my_bookings.php');
        exit;
    }
    
    // Verify ownership
    if ($booking['learner_id'] != $_SESSION['user_id']) {
        set_flash('You do not have permission to cancel this booking.', 'danger');
        redirect('my_bookings.php');
        exit;
    }
    
    // Check if booking can be cancelled (not already cancelled)
    if ($booking['status'] === 'cancelled') {
        set_flash('This booking has already been cancelled.', 'warning');
        redirect('my_bookings.php');
        exit;
    }
    
    // Cancel the booking
    $result = Booking::cancelBooking($booking_id);
    
    if ($result) {
        // If booking was accepted, restore capacity
        if ($booking['status'] === 'accepted') {
            $session = Session::getSessionById($booking['session_id']);
            if ($session) {
                $new_capacity = $session['capacity_remaining'] + 1;
                Session::updateSession($session['session_id'], ['capacity_remaining' => $new_capacity]);
            }
        }
        
        set_flash('Booking cancelled successfully.', 'success');
    } else {
        set_flash('Failed to cancel booking. Please try again.', 'danger');
    }
    
} else {
    set_flash('Invalid request method.', 'danger');
}

redirect('my_bookings.php');
