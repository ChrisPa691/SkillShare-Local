<?php
/**
 * Booking Controller
 * Handles booking-related business logic
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_guard.php';

class BookingController {
    
    /**
     * Accept a booking request
     * 
     * @param int $booking_id - Booking ID
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function acceptBooking($booking_id) {
        // Get booking details
        $booking = Booking::getBookingById($booking_id);
        
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found.'
            ];
        }
        
        // Verify booking is pending
        if ($booking['status'] !== 'pending') {
            return [
                'success' => false,
                'message' => 'Booking has already been processed.'
            ];
        }
        
        // Get session details
        $session = Session::getSessionById($booking['session_id']);
        
        if (!$session) {
            return [
                'success' => false,
                'message' => 'Session not found.'
            ];
        }
        
        // Check capacity
        if ($session['capacity_remaining'] <= 0) {
            return [
                'success' => false,
                'message' => 'Session is full.'
            ];
        }
        
        // Accept booking
        $result = Booking::acceptBooking($booking_id);
        
        if ($result) {
            // Update session capacity
            $new_capacity = $session['capacity_remaining'] - 1;
            Session::updateSession($session['session_id'], ['capacity_remaining' => $new_capacity]);
            
            return [
                'success' => true,
                'message' => 'Booking accepted successfully!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to accept booking.'
            ];
        }
    }
    
    /**
     * Decline a booking request
     * 
     * @param int $booking_id - Booking ID
     * @param string $reason - Decline reason (optional)
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function declineBooking($booking_id, $reason = null) {
        // Get booking details
        $booking = Booking::getBookingById($booking_id);
        
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found.'
            ];
        }
        
        // Verify booking is pending
        if ($booking['status'] !== 'pending') {
            return [
                'success' => false,
                'message' => 'Booking has already been processed.'
            ];
        }
        
        // Decline booking
        $result = Booking::updateBookingStatus($booking_id, 'declined');
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Booking declined.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to decline booking.'
            ];
        }
    }
    
    /**
     * Cancel a booking (by learner)
     * 
     * @param int $booking_id - Booking ID
     * @param int $learner_id - Learner ID (for verification)
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function cancelBooking($booking_id, $learner_id) {
        // Get booking details
        $booking = Booking::getBookingById($booking_id);
        
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found.'
            ];
        }
        
        // Verify ownership
        if ($booking['learner_id'] != $learner_id) {
            return [
                'success' => false,
                'message' => 'You do not have permission to cancel this booking.'
            ];
        }
        
        // Can only cancel pending or accepted bookings
        if (!in_array($booking['status'], ['pending', 'accepted'])) {
            return [
                'success' => false,
                'message' => 'This booking cannot be canceled.'
            ];
        }
        
        // If booking was accepted, restore session capacity
        if ($booking['status'] === 'accepted') {
            $session = Session::getSessionById($booking['session_id']);
            if ($session) {
                $new_capacity = $session['capacity_remaining'] + 1;
                Session::updateSession($session['session_id'], ['capacity_remaining' => $new_capacity]);
            }
        }
        
        // Cancel booking
        $result = Booking::cancelBooking($booking_id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Booking canceled successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to cancel booking.'
            ];
        }
    }
}
