<?php
/**
 * Rating Controller
 * Handles rating and review submission logic
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Rating.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_guard.php';

class RatingController {
    
    /**
     * Submit a rating and review
     * 
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function submitRating() {
        // Validate required fields
        if (empty($_POST['session_id']) || empty($_POST['rating'])) {
            return [
                'success' => false,
                'message' => 'Session and rating are required.'
            ];
        }
        
        $session_id = intval($_POST['session_id']);
        $rating = intval($_POST['rating']);
        $comment = trim($_POST['comment'] ?? '');
        $learner_id = get_user_id();
        
        // Validate rating value
        if ($rating < 1 || $rating > 5) {
            return [
                'success' => false,
                'message' => 'Rating must be between 1 and 5.'
            ];
        }
        
        // Check if session exists
        $session = Session::getSessionById($session_id);
        if (!$session) {
            return [
                'success' => false,
                'message' => 'Session not found.'
            ];
        }
        
        // Check if session is completed
        if ($session['status'] !== 'completed') {
            return [
                'success' => false,
                'message' => 'You can only rate completed sessions.'
            ];
        }
        
        // Check if user has accepted booking for this session
        $booking = Booking::getBookingByLearnerAndSession($learner_id, $session_id);
        if (!$booking || $booking['status'] !== 'accepted') {
            return [
                'success' => false,
                'message' => 'You can only rate sessions you attended.'
            ];
        }
        
        // Check if rating already exists
        $existing_rating = Rating::getRatingByLearnerAndSession($learner_id, $session_id);
        if ($existing_rating) {
            return [
                'success' => false,
                'message' => 'You have already rated this session.'
            ];
        }
        
        // Create rating
        $rating_data = [
            'session_id' => $session_id,
            'learner_id' => $learner_id,
            'rating' => $rating,
            'comment' => $comment
        ];
        
        $rating_id = Rating::createRating($rating_data);
        
        if ($rating_id) {
            return [
                'success' => true,
                'message' => 'Thank you for your rating!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to submit rating. Please try again.'
            ];
        }
    }
    
    /**
     * Update an existing rating
     * 
     * @param int $rating_id - Rating ID
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function updateRating($rating_id) {
        // Validate required fields
        if (empty($_POST['rating'])) {
            return [
                'success' => false,
                'message' => 'Rating is required.'
            ];
        }
        
        $rating = intval($_POST['rating']);
        $comment = trim($_POST['comment'] ?? '');
        $learner_id = get_user_id();
        
        // Validate rating value
        if ($rating < 1 || $rating > 5) {
            return [
                'success' => false,
                'message' => 'Rating must be between 1 and 5.'
            ];
        }
        
        // Get existing rating to verify ownership
        $existing_rating = Rating::getRatingById($rating_id);
        if (!$existing_rating) {
            return [
                'success' => false,
                'message' => 'Rating not found.'
            ];
        }
        
        if ($existing_rating['learner_id'] != $learner_id) {
            return [
                'success' => false,
                'message' => 'You do not have permission to update this rating.'
            ];
        }
        
        // Update rating
        $update_data = [
            'rating' => $rating,
            'comment' => $comment
        ];
        
        $result = Rating::updateRating($rating_id, $update_data);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Rating updated successfully!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update rating. Please try again.'
            ];
        }
    }
    
    /**
     * Delete a rating
     * 
     * @param int $rating_id - Rating ID
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function deleteRating($rating_id) {
        $learner_id = get_user_id();
        
        // Get existing rating to verify ownership
        $existing_rating = Rating::getRatingById($rating_id);
        if (!$existing_rating) {
            return [
                'success' => false,
                'message' => 'Rating not found.'
            ];
        }
        
        if ($existing_rating['learner_id'] != $learner_id) {
            return [
                'success' => false,
                'message' => 'You do not have permission to delete this rating.'
            ];
        }
        
        // Delete rating
        $result = Rating::deleteRating($rating_id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Rating deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete rating. Please try again.'
            ];
        }
    }
}
