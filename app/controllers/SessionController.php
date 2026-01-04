<?php
/**
 * Session Controller
 * Handles all session-related business logic and form processing
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_guard.php';

class SessionController {
    
    /**
     * Handle session creation
     * 
     * @return array - ['success' => bool, 'message' => string, 'session_id' => int|null]
     */
    public static function handleCreate() {
        // Ensure user is logged in and is an instructor
        require_login();
        if (!is_instructor()) {
            return [
                'success' => false,
                'message' => 'Only instructors can create sessions',
                'session_id' => null
            ];
        }
        
        // Validate required fields
        $validation = self::validateSessionData($_POST, true);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => implode('<br>', $validation['errors']),
                'session_id' => null
            ];
        }
        
        // Handle photo upload
        $photoUrl = self::handlePhotoUpload();
        
        // Prepare session data
        $sessionData = [
            'instructor_id' => get_user_id(),
            'category_id' => (int)$_POST['category_id'],
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'duration_minutes' => (int)$_POST['duration_minutes'],
            'fee_type' => $_POST['fee_type'],
            'fee_amount' => $_POST['fee_type'] === 'paid' ? (float)$_POST['fee_amount'] : 0,
            'location_type' => $_POST['location_type'],
            'event_datetime' => $_POST['event_datetime'],
            'total_capacity' => (int)$_POST['total_capacity'],
            'sustainability_description' => trim($_POST['sustainability_description'] ?? '')
        ];
        
        // Add location-specific fields
        if ($_POST['location_type'] === 'online') {
            $sessionData['online_link'] = trim($_POST['online_link']);
            $sessionData['city'] = null;
            $sessionData['address'] = null;
        } else {
            $sessionData['city'] = trim($_POST['city']);
            $sessionData['address'] = trim($_POST['address']);
            $sessionData['online_link'] = null;
        }
        
        // Add photo URL if uploaded
        if ($photoUrl) {
            $sessionData['photo_url'] = $photoUrl;
        }
        
        // Create session
        $sessionId = Session::createSession($sessionData);
        
        if ($sessionId) {
            return [
                'success' => true,
                'message' => 'Session created successfully!',
                'session_id' => $sessionId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create session. Please try again.',
                'session_id' => null
            ];
        }
    }
    
    /**
     * Handle session update
     * 
     * @param int $session_id - Session ID to update
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function handleUpdate($session_id) {
        // Ensure user is logged in
        require_login();
        
        // Verify session exists and user owns it
        $session = Session::getSessionById($session_id);
        if (!$session) {
            return [
                'success' => false,
                'message' => 'Session not found'
            ];
        }
        
        // Check ownership (or admin)
        if ($session['instructor_id'] != get_user_id() && !is_admin()) {
            return [
                'success' => false,
                'message' => 'You do not have permission to edit this session'
            ];
        }
        
        // Validate data
        $validation = self::validateSessionData($_POST, false);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => implode('<br>', $validation['errors'])
            ];
        }
        
        // Handle photo upload if new photo provided
        $photoUrl = self::handlePhotoUpload();
        
        // Prepare update data
        $updateData = [];
        
        if (isset($_POST['category_id'])) $updateData['category_id'] = (int)$_POST['category_id'];
        if (isset($_POST['title'])) $updateData['title'] = trim($_POST['title']);
        if (isset($_POST['description'])) $updateData['description'] = trim($_POST['description']);
        if (isset($_POST['duration_minutes'])) $updateData['duration_minutes'] = (int)$_POST['duration_minutes'];
        
        if (isset($_POST['fee_type'])) {
            $updateData['fee_type'] = $_POST['fee_type'];
            $updateData['fee_amount'] = $_POST['fee_type'] === 'paid' ? (float)$_POST['fee_amount'] : 0;
        }
        
        if (isset($_POST['location_type'])) {
            $updateData['location_type'] = $_POST['location_type'];
            
            if ($_POST['location_type'] === 'online') {
                $updateData['online_link'] = trim($_POST['online_link']);
                $updateData['city'] = null;
                $updateData['address'] = null;
            } else {
                $updateData['city'] = trim($_POST['city']);
                $updateData['address'] = trim($_POST['address']);
                $updateData['online_link'] = null;
            }
        }
        
        if (isset($_POST['event_datetime'])) $updateData['event_datetime'] = $_POST['event_datetime'];
        if (isset($_POST['total_capacity'])) $updateData['total_capacity'] = (int)$_POST['total_capacity'];
        if (isset($_POST['sustainability_description'])) $updateData['sustainability_description'] = trim($_POST['sustainability_description']);
        
        // Add photo URL if uploaded
        if ($photoUrl) {
            $updateData['photo_url'] = $photoUrl;
        }
        
        // Update session
        $updated = Session::updateSession($session_id, $updateData);
        
        if ($updated) {
            return [
                'success' => true,
                'message' => 'Session updated successfully!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update session. Please try again.'
            ];
        }
    }
    
    /**
     * Handle session deletion (soft delete)
     * 
     * @param int $session_id - Session ID to delete
     * @return array - ['success' => bool, 'message' => string]
     */
    public static function handleDelete($session_id) {
        require_login();
        
        $session = Session::getSessionById($session_id);
        if (!$session) {
            return ['success' => false, 'message' => 'Session not found'];
        }
        
        if ($session['instructor_id'] != get_user_id() && !is_admin()) {
            return ['success' => false, 'message' => 'You do not have permission to delete this session'];
        }
        
        $deleted = Session::deleteSession($session_id);
        
        if ($deleted) {
            return ['success' => true, 'message' => 'Session canceled successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to cancel session. Please try again.'];
        }
    }
    
    /**
     * Validate session data
     * 
     * @param array $data - POST data
     * @param bool $isCreate - True for create, false for update
     * @return array - ['valid' => bool, 'errors' => array]
     */
    private static function validateSessionData($data, $isCreate = true) {
        $errors = [];
        
        if ($isCreate) {
            if (empty($data['category_id'])) $errors[] = 'Category is required';
            if (empty($data['title']) || strlen(trim($data['title'])) < 3) $errors[] = 'Title must be at least 3 characters';
            if (empty($data['description']) || strlen(trim($data['description'])) < 10) $errors[] = 'Description must be at least 10 characters';
            if (empty($data['duration_minutes']) || $data['duration_minutes'] < 15) $errors[] = 'Duration must be at least 15 minutes';
            if (empty($data['fee_type']) || !in_array($data['fee_type'], ['free', 'paid'])) $errors[] = 'Invalid fee type';
            if (empty($data['location_type']) || !in_array($data['location_type'], ['online', 'in-person'])) $errors[] = 'Invalid location type';
            if (empty($data['event_datetime'])) $errors[] = 'Event date and time are required';
            if (empty($data['total_capacity']) || $data['total_capacity'] < 1) $errors[] = 'Capacity must be at least 1';
        }
        
        if (!empty($data['fee_type']) && $data['fee_type'] === 'paid') {
            if (empty($data['fee_amount']) || $data['fee_amount'] <= 0) $errors[] = 'Fee amount is required for paid sessions';
        }
        
        if (!empty($data['location_type'])) {
            if ($data['location_type'] === 'online') {
                if (empty($data['online_link'])) $errors[] = 'Online meeting link is required for online sessions';
            } else {
                if (empty($data['city'])) $errors[] = 'City is required for in-person sessions';
                if (empty($data['address'])) $errors[] = 'Address is required for in-person sessions';
            }
        }
        
        if (!empty($data['event_datetime'])) {
            $eventTime = strtotime($data['event_datetime']);
            if ($eventTime < time()) $errors[] = 'Event date must be in the future';
        }
        
        return ['valid' => empty($errors), 'errors' => $errors];
    }
    
    /**
     * Handle photo upload
     * 
     * @return string|null - Photo URL or null if no upload
     */
    private static function handlePhotoUpload() {
        if (!isset($_FILES['session_photo']) || $_FILES['session_photo']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        
        if ($_FILES['session_photo']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $file = $_FILES['session_photo'];
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }
        
        $uploadDir = __DIR__ . '/../../public/assets/images/sessions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'session_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return 'assets/images/sessions/' . $filename;
        }
        
        return null;
    }
    
    /**
     * Get filter data for sessions page
     * 
     * @return array - Filters from GET/POST
     */
    public static function getFilters() {
        $filters = [];
        
        if (!empty($_GET['search']) || !empty($_POST['search'])) {
            $filters['search'] = trim($_GET['search'] ?? $_POST['search']);
        }
        if (!empty($_GET['category_id']) || !empty($_POST['category_id'])) {
            $filters['category_id'] = (int)($_GET['category_id'] ?? $_POST['category_id']);
        }
        if (!empty($_GET['location_type']) || !empty($_POST['location_type'])) {
            $filters['location_type'] = $_GET['location_type'] ?? $_POST['location_type'];
        }
        if (!empty($_GET['fee_type']) || !empty($_POST['fee_type'])) {
            $filters['fee_type'] = $_GET['fee_type'] ?? $_POST['fee_type'];
        }
        if (!empty($_GET['city']) || !empty($_POST['city'])) {
            $filters['city'] = trim($_GET['city'] ?? $_POST['city']);
        }
        if (isset($_GET['status']) || isset($_POST['status'])) {
            $filters['status'] = $_GET['status'] ?? $_POST['status'];
        }
        
        return $filters;
    }
    
    /**
     * Get all categories for dropdown
     * 
     * @return array - Categories
     */
    public static function getCategories() {
        return db_select('Categories', [], 0) ?: [];
    }
    
    /**
     * Get all unique cities from sessions
     * 
     * @return array - Cities
     */
    public static function getCities() {
        global $conn;
        
        try {
            $sql = "SELECT DISTINCT city FROM skill_sessions WHERE city IS NOT NULL ORDER BY city ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting cities: " . $e->getMessage());
            return [];
        }
    }
}

