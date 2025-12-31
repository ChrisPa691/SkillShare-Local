<?php
/**
 * Session Controller
 * Handles all session-related business logic and request processing
 */

require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Rating.php';
require_once __DIR__ . '/../includes/helpers.php';

class SessionController {
    
    /**
     * Display all sessions with filters
     * GET /sessions.php
     * 
     * @return void - Renders sessions view
     */
    public static function index() {
        // TODO: Get filter parameters from $_GET:
        // - category_id (int or null)
        // - location_type ('online', 'in-person', or null)
        // - fee_type ('free', 'paid', or null)
        // - city (string or null)
        // - search (string or null)
        
        // TODO: Build filters array from $_GET parameters
        // TODO: Call Session::getAllSessions($filters)
        
        // TODO: Get all categories for filter dropdown
        // - Use db_select('Categories', [], 'ORDER BY category_name ASC')
        
        // TODO: Get all unique cities for filter dropdown
        // - SELECT DISTINCT city FROM skill_sessions WHERE city IS NOT NULL
        
        // TODO: Store results in variables for view
        // - $sessions
        // - $categories
        // - $cities
        // - $current_filters (for maintaining filter state)
        
        // TODO: Include view file (sessions.php will access these variables)
    }
    
    /**
     * Display single session detail
     * GET /session_view.php?id=123
     * 
     * @param int $session_id - Session ID from $_GET
     * @return void - Renders session detail view
     */
    public static function show($session_id) {
        // TODO: Validate $session_id is numeric
        // TODO: Call Session::getSessionById($session_id)
        // TODO: If not found, redirect to sessions.php with error message
        
        // TODO: Get instructor details from Users table
        // - Use User::getUserById($session['instructor_id'])
        
        // TODO: Get all ratings for this session
        // - Use Rating::getRatingsBySession($session_id)
        
        // TODO: If user is logged in, check if they already booked this session
        // - Use Booking::getBookingByLearnerAndSession($_SESSION['user_id'], $session_id)
        
        // TODO: Check if session is full (capacity_remaining <= 0)
        
        // TODO: Store results in variables:
        // - $session
        // - $instructor
        // - $ratings
        // - $user_booking (or null)
        // - $is_full
        // - $can_book (learner, not full, not already booked)
        
        // TODO: Include view file (session_view.php will access these variables)
    }
    
    /**
     * Create new session (instructor only)
     * POST /instructor_dashboard.php (create session form)
     * 
     * @return void - Redirects after creation
     */
    public static function create() {
        // TODO: Check if user is logged in and is instructor
        // - if (!is_logged_in() || get_user_role() !== 'instructor') redirect
        
        // TODO: Validate CSRF token (security)
        
        // TODO: Validate all required fields from $_POST:
        // - title (required, 3-100 chars)
        // - description (required, min 20 chars)
        // - category_id (required, must exist)
        // - duration_minutes (required, 15-480 range)
        // - fee_type (required, 'free' or 'paid')
        // - fee_amount (required if paid, must be > 0)
        // - location_type (required, 'online' or 'in-person')
        // - city (required if in-person)
        // - address (required if in-person)
        // - online_link (required if online, must be valid URL)
        // - event_datetime (required, must be future)
        // - total_capacity (required, 1-100 range)
        // - sustainability_description (optional)
        
        // TODO: Sanitize all inputs using escape() or htmlspecialchars()
        
        // TODO: Handle photo upload if provided
        // - Validate file type (jpg, png, gif only)
        // - Validate file size (max 2MB)
        // - Generate unique filename
        // - Move to public/assets/images/sessions/
        // - Store relative path in database
        
        // TODO: Build $data array with all fields
        // - Add instructor_id = $_SESSION['user_id']
        // - Set capacity_remaining = total_capacity
        // - Set status = 'upcoming'
        // - Add created_at timestamp
        
        // TODO: Call Session::createSession($data)
        
        // TODO: If successful:
        // - Set success message in session
        // - Redirect to instructor_dashboard.php
        
        // TODO: If failed:
        // - Set error message in session
        // - Redirect back to form with old input
    }
    
    /**
     * Update existing session (instructor only)
     * POST /instructor_dashboard.php (edit session form)
     * 
     * @param int $session_id - Session ID to update
     * @return void - Redirects after update
     */
    public static function update($session_id) {
        // TODO: Check if user is logged in and is instructor
        
        // TODO: Get existing session
        // TODO: Verify that current user is the instructor of this session
        // - if ($session['instructor_id'] !== $_SESSION['user_id']) redirect with error
        
        // TODO: Validate CSRF token
        
        // TODO: Validate all fields (same as create, but all optional except session_id)
        
        // TODO: Sanitize inputs
        
        // TODO: Handle photo upload if new photo provided
        // - Delete old photo file if it exists
        // - Upload new photo
        
        // TODO: Build $data array with only changed fields
        // - Add updated_at timestamp
        
        // TODO: Call Session::updateSession($session_id, $data)
        
        // TODO: If successful, redirect with success message
        // TODO: If failed, redirect with error message
    }
    
    /**
     * Cancel/Delete session (instructor or admin)
     * POST /instructor_dashboard.php or /admin_dashboard.php
     * 
     * @param int $session_id - Session ID to cancel
     * @return void - Redirects after cancellation
     */
    public static function cancel($session_id) {
        // TODO: Check if user is logged in
        
        // TODO: Get session details
        
        // TODO: Check authorization:
        // - If instructor: must be session owner
        // - If admin: can cancel any session
        // - Else: redirect with error
        
        // TODO: Validate CSRF token
        
        // TODO: Get cancellation reason from $_POST (optional)
        
        // TODO: Check if session has bookings
        // - If yes, notify learners (future enhancement with email)
        
        // TODO: Call Session::deleteSession($session_id, $reason)
        
        // TODO: Log admin action if admin cancelled it
        // - INSERT into admin_actions
        
        // TODO: Redirect with success message
    }
    
    /**
     * Mark session as completed (system/admin)
     * POST /admin_dashboard.php
     * 
     * @param int $session_id - Session ID
     * @return void
     */
    public static function markCompleted($session_id) {
        // TODO: Check if user is admin
        
        // TODO: Get session details
        // TODO: Verify session event_datetime has passed
        
        // TODO: Update status to 'completed'
        // - Use Session::updateSession($session_id, ['status' => 'completed'])
        
        // TODO: Log admin action
        
        // TODO: Redirect with success message
    }
    
    /**
     * Get sessions for instructor dashboard (AJAX)
     * GET /instructor_dashboard.php?ajax=get_my_sessions
     * 
     * @param int $instructor_id - Instructor user ID
     * @return json - JSON response with sessions
     */
    public static function getInstructorSessions($instructor_id) {
        // TODO: Check if user is logged in and is instructor
        
        // TODO: Get filter from $_GET (upcoming, completed, all)
        
        // TODO: Call Session::getSessionsByInstructor($instructor_id, $status)
        
        // TODO: Return JSON response
        // - header('Content-Type: application/json')
        // - echo json_encode(['success' => true, 'sessions' => $sessions])
    }
    
    /**
     * Search sessions (AJAX)
     * GET /sessions.php?ajax=search&q=keyword
     * 
     * @return json - JSON response with search results
     */
    public static function search() {
        // TODO: Get search keyword from $_GET['q']
        // TODO: Sanitize and validate keyword (min 2 chars)
        
        // TODO: Call Session::searchSessions($keyword, 20)
        
        // TODO: Return JSON response with results
        // - Format for autocomplete or live search results
    }
    
    /**
     * Get session stats for dashboard
     * Used by instructor_dashboard.php
     * 
     * @param int $instructor_id - Instructor user ID
     * @return array - Statistics array
     */
    public static function getStats($instructor_id) {
        // TODO: Call Session::getInstructorStats($instructor_id)
        
        // TODO: Return stats array for display in dashboard cards
    }
    
    /**
     * Validate session form data (helper method)
     * 
     * @param array $data - Form data from $_POST
     * @param bool $is_update - True if updating, false if creating
     * @return array - Array of validation errors (empty if valid)
     */
    private static function validateSessionData($data, $is_update = false) {
        // TODO: Initialize $errors array
        
        // TODO: Validate title
        // - Required if not update
        // - Length 3-100 chars
        // - No special characters except basic punctuation
        
        // TODO: Validate description
        // - Required if not update
        // - Min 20 chars, max 5000 chars
        
        // TODO: Validate category_id
        // - Must exist in Categories table
        
        // TODO: Validate duration_minutes
        // - Must be integer
        // - Range 15-480 (15 min to 8 hours)
        
        // TODO: Validate fee_type and fee_amount
        // - fee_type must be 'free' or 'paid'
        // - If 'free', fee_amount must be 0
        // - If 'paid', fee_amount must be > 0
        
        // TODO: Validate location_type
        // - Must be 'online' or 'in-person'
        // - If 'online', online_link is required and must be valid URL
        // - If 'in-person', city and address are required
        
        // TODO: Validate event_datetime
        // - Must be valid datetime format
        // - Must be in the future (at least 1 hour from now)
        
        // TODO: Validate capacity
        // - Must be integer
        // - Range 1-100
        // - If updating, capacity_remaining should not exceed new total_capacity
        
        // TODO: Return $errors array
    }
    
    /**
     * Handle photo upload (helper method)
     * 
     * @param array $file - $_FILES['photo']
     * @return string|false - Relative path to uploaded photo or false on failure
     */
    private static function handlePhotoUpload($file) {
        // TODO: Check if file was uploaded
        // - if ($file['error'] !== UPLOAD_ERR_OK) return false
        
        // TODO: Validate file type
        // - $allowed = ['image/jpeg', 'image/png', 'image/gif']
        // - if (!in_array($file['type'], $allowed)) return false
        
        // TODO: Validate file size (max 2MB)
        // - if ($file['size'] > 2 * 1024 * 1024) return false
        
        // TODO: Generate unique filename
        // - $extension = pathinfo($file['name'], PATHINFO_EXTENSION)
        // - $filename = uniqid('session_') . '.' . $extension
        
        // TODO: Create upload directory if not exists
        // - $upload_dir = __DIR__ . '/../../public/assets/images/sessions/'
        // - if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true)
        
        // TODO: Move uploaded file
        // - move_uploaded_file($file['tmp_name'], $upload_dir . $filename)
        
        // TODO: Return relative path for database
        // - return 'assets/images/sessions/' . $filename
    }
}
