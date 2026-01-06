<?php
/**
 * Session Model
 * Handles all database operations related to skill sessions
 */

require_once __DIR__ . '/../config/database.php';

class Session {
    
    /**
     * Get all sessions with optional filters
     * 
     * @param array $filters - Optional filters (category_id, location_type, fee_type, city, status, search)
     * @param int $limit - Number of results to return (0 = all)
     * @return array|false - Array of sessions or false on failure
     */
    public static function getAllSessions($filters = [], $limit = 0) {
        global $conn;
        
        $sql = "SELECT s.*, 
                       u.full_name as instructor_name,
                       c.name as category_name,
                       COALESCE(AVG(r.rating), 0) as average_rating
                FROM skill_sessions s
                INNER JOIN Users u ON s.instructor_id = u.user_id
                INNER JOIN Categories c ON s.category_id = c.category_id
                LEFT JOIN ratings r ON s.session_id = r.session_id
                WHERE 1=1";
        
        $params = [];
        
        // Apply filters (case-insensitive search)
        if (!empty($filters['search'])) {
            $sql .= " AND (LOWER(s.title) LIKE LOWER(?) OR LOWER(s.description) LIKE LOWER(?))";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND s.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['location_type'])) {
            $sql .= " AND s.location_type = ?";
            $params[] = $filters['location_type'];
        }
        
        if (!empty($filters['fee_type'])) {
            $sql .= " AND s.fee_type = ?";
            $params[] = $filters['fee_type'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND s.city = ?";
            $params[] = $filters['city'];
        }
        
        // Date range filters
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(s.event_datetime) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(s.event_datetime) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Availability filter
        if (!empty($filters['has_availability'])) {
            $sql .= " AND s.capacity_remaining > 0";
        }
        
        // Default to upcoming sessions if status not specified
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        } elseif (!isset($filters['status'])) {
            // Default to upcoming if no status filter provided
            $sql .= " AND s.status = 'upcoming'";
        }
        
        $sql .= " GROUP BY s.session_id, u.user_id, c.category_id ORDER BY s.event_datetime ASC";
        
        // Add LIMIT directly (cannot be bound as parameter in all MySQL versions)
        if ($limit > 0) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllSessions: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single session by ID
     * 
     * @param int $session_id - Session ID
     * @return array|false - Session data or false if not found
     */
    public static function getSessionById($session_id) {
        global $conn;
        
        $sql = "SELECT s.*, 
                       u.full_name as instructor_name,
                       u.email as instructor_email,
                       u.city as instructor_city,
                       c.name as category_name,
                       COALESCE(AVG(r.rating), 0) as average_rating,
                       COUNT(r.rating_id) as total_ratings
                FROM skill_sessions s
                INNER JOIN Users u ON s.instructor_id = u.user_id
                INNER JOIN Categories c ON s.category_id = c.category_id
                LEFT JOIN ratings r ON s.session_id = r.session_id
                WHERE s.session_id = ?
                GROUP BY s.session_id, u.user_id, c.category_id";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$session_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Return false only if no result found (not if query succeeded but returned empty)
            return $result !== false ? $result : false;
        } catch (PDOException $e) {
            error_log("Error in getSessionById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all sessions by a specific instructor
     * 
     * @param int $instructor_id - Instructor user ID
     * @param string $status - Filter by status (optional, default = all)
     * @return array|false - Array of sessions or false
     */
    public static function getSessionsByInstructor($instructor_id, $status = null) {
        global $conn;
        
        $sql = "SELECT s.*, 
                       c.name as category_name,
                       COALESCE(AVG(r.rating), 0) as average_rating
                FROM skill_sessions s
                INNER JOIN Categories c ON s.category_id = c.category_id
                LEFT JOIN ratings r ON s.session_id = r.session_id
                WHERE s.instructor_id = ?";
        
        $params = [$instructor_id];
        
        if ($status !== null) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY s.session_id, c.category_id ORDER BY s.event_datetime DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getSessionsByInstructor: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all sessions in a specific category
     * 
     * @param int $category_id - Category ID
     * @param int $limit - Limit results (0 = all)
     * @return array|false - Array of sessions or false
     */
    public static function getSessionsByCategory($category_id, $limit = 0) {
        global $conn;
        
        $sql = "SELECT s.*, 
                       u.full_name as instructor_name,
                       c.name as category_name,
                       COALESCE(AVG(r.rating), 0) as average_rating
                FROM skill_sessions s
                INNER JOIN Users u ON s.instructor_id = u.user_id
                INNER JOIN Categories c ON s.category_id = c.category_id
                LEFT JOIN ratings r ON s.session_id = r.session_id
                WHERE s.category_id = ? AND s.status = 'upcoming'
                GROUP BY s.session_id, u.user_id, c.category_id
                ORDER BY s.event_datetime ASC";
        
        $params = [$category_id];
        
        if ($limit > 0) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getSessionsByCategory: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new session
     * 
     * @param array $data - Session data (all required fields)
     * @return int|false - New session ID or false on failure
     */
    public static function createSession($data) {
        // Set defaults
        $data['status'] = $data['status'] ?? 'upcoming';
        $data['capacity_remaining'] = $data['capacity_remaining'] ?? $data['total_capacity'];
        
        // Ensure fee_amount is 0 for free sessions
        if ($data['fee_type'] === 'free') {
            $data['fee_amount'] = 0;
        }
        
        // Set null for location fields based on location_type
        if ($data['location_type'] === 'online') {
            $data['city'] = null;
            $data['address'] = null;
        } else {
            $data['online_link'] = null;
        }
        
        try {
            return db_insert('skill_sessions', $data);
        } catch (Exception $e) {
            error_log("Error in createSession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing session
     * 
     * @param int $session_id - Session ID to update
     * @param array $data - Fields to update
     * @return bool - True on success, false on failure
     */
    public static function updateSession($session_id, $data) {
        // Ensure fee_amount is 0 for free sessions
        if (isset($data['fee_type']) && $data['fee_type'] === 'free') {
            $data['fee_amount'] = 0;
        }
        
        try {
            return db_update('skill_sessions', $data, ['session_id' => $session_id]);
        } catch (Exception $e) {
            error_log("Error in updateSession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete/Cancel a session
     * 
     * @param int $session_id - Session ID to delete
     * @param string $reason - Reason for cancellation (optional)
     * @return bool - True on success, false on failure
     */
    public static function deleteSession($session_id, $reason = null) {
        // Soft delete - set status to canceled
        try {
            $data = [
                'status' => 'canceled',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            return db_update('skill_sessions', $data, ['session_id' => $session_id]);
        } catch (Exception $e) {
            error_log("Error in deleteSession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get upcoming sessions (helper method)
     * 
     * @param int $limit - Number of sessions to return
     * @return array|false - Array of upcoming sessions
     */
    public static function getUpcomingSessions($limit = 10) {
        global $conn;
        
        $sql = "SELECT s.*, 
                       u.full_name as instructor_name,
                       c.name as category_name,
                       COALESCE(AVG(r.rating), 0) as average_rating
                FROM skill_sessions s
                INNER JOIN Users u ON s.instructor_id = u.user_id
                INNER JOIN Categories c ON s.category_id = c.category_id
                LEFT JOIN ratings r ON s.session_id = r.session_id
                WHERE s.status = 'upcoming' AND s.event_datetime > NOW()
                GROUP BY s.session_id, u.user_id, c.category_id
                ORDER BY s.event_datetime ASC
                LIMIT " . intval($limit);
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUpcomingSessions: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get completed sessions (for ratings)
     * 
     * @param int $learner_id - Learner ID (optional)
     * @return array|false - Array of completed sessions
     */
    public static function getCompletedSessions($learner_id = null) {
        global $conn;
        
        if ($learner_id !== null) {
            $sql = "SELECT s.*, 
                           u.full_name as instructor_name,
                           c.name as category_name,
                           b.booking_id
                    FROM skill_sessions s
                    INNER JOIN Users u ON s.instructor_id = u.user_id
                    INNER JOIN Categories c ON s.category_id = c.category_id
                    INNER JOIN bookings b ON s.session_id = b.session_id
                    WHERE s.status = 'completed' 
                          AND b.learner_id = ? 
                          AND b.status = 'accepted'
                    ORDER BY s.event_datetime DESC";
            $params = [$learner_id];
        } else {
            $sql = "SELECT s.*, 
                           u.full_name as instructor_name,
                           c.name as category_name
                    FROM skill_sessions s
                    INNER JOIN Users u ON s.instructor_id = u.user_id
                    INNER JOIN Categories c ON s.category_id = c.category_id
                    WHERE s.status = 'completed'
                    ORDER BY s.event_datetime DESC";
            $params = [];
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCompletedSessions: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search sessions by keyword
     * 
     * @param string $keyword - Search term
     * @param int $limit - Limit results
     * @return array|false - Array of matching sessions
     */
    public static function searchSessions($keyword, $limit = 20) {
        global $conn;
        
        $sql = "SELECT s.*, 
                       u.full_name as instructor_name,
                       c.name as category_name,
                       COALESCE(AVG(r.rating), 0) as average_rating,
                       CASE 
                           WHEN s.title LIKE ? THEN 1
                           ELSE 2
                       END as relevance
                FROM skill_sessions s
                INNER JOIN Users u ON s.instructor_id = u.user_id
                INNER JOIN Categories c ON s.category_id = c.category_id
                LEFT JOIN ratings r ON s.session_id = r.session_id
                WHERE (s.title LIKE ? OR s.description LIKE ?)
                      AND s.status = 'upcoming'
                GROUP BY s.session_id, u.user_id, c.category_id
                ORDER BY relevance ASC, s.event_datetime ASC
                LIMIT " . intval($limit);
        
        $searchTerm = '%' . $keyword . '%';
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in searchSessions: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update session capacity when booking is confirmed
     * 
     * @param int $session_id - Session ID
     * @param int $seats - Number of seats to reduce
     * @return bool - True on success, false on failure
     */
    public static function updateCapacity($session_id, $seats = 1) {
        global $conn;
        
        try {
            // Get current capacity
            $session = self::getSessionById($session_id);
            if (!$session) {
                return false;
            }
            
            if ($session['capacity_remaining'] < $seats) {
                return false; // Not enough seats
            }
            
            // Update capacity
            $sql = "UPDATE skill_sessions 
                    SET capacity_remaining = capacity_remaining - ?,
                        updated_at = NOW()
                    WHERE session_id = ?";
            
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$seats, $session_id]);
        } catch (PDOException $e) {
            error_log("Error in updateCapacity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get session statistics for instructor dashboard
     * 
     * @param int $instructor_id - Instructor user ID
     * @return array - Statistics array
     */
    public static function getInstructorStats($instructor_id) {
        global $conn;
        
        try {
            // Get session counts
            $sql = "SELECT 
                        COUNT(*) as total_sessions,
                        SUM(CASE WHEN status = 'upcoming' THEN 1 ELSE 0 END) as upcoming_sessions,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions
                    FROM skill_sessions
                    WHERE instructor_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$instructor_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get total learners (confirmed bookings)
            $sql = "SELECT COUNT(DISTINCT b.learner_id) as total_learners
                    FROM bookings b
                    INNER JOIN skill_sessions s ON b.session_id = s.session_id
                    WHERE s.instructor_id = ? AND b.status = 'accepted'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$instructor_id]);
            $learner_count = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get average rating
            $sql = "SELECT COALESCE(AVG(r.rating), 0) as average_rating
                    FROM ratings r
                    INNER JOIN skill_sessions s ON r.session_id = s.session_id
                    WHERE s.instructor_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$instructor_id]);
            $rating = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_sessions' => (int)$stats['total_sessions'],
                'upcoming_sessions' => (int)$stats['upcoming_sessions'],
                'completed_sessions' => (int)$stats['completed_sessions'],
                'total_learners' => (int)$learner_count['total_learners'],
                'average_rating' => round((float)$rating['average_rating'], 2)
            ];
        } catch (PDOException $e) {
            error_log("Error in getInstructorStats: " . $e->getMessage());
            return [
                'total_sessions' => 0,
                'upcoming_sessions' => 0,
                'completed_sessions' => 0,
                'total_learners' => 0,
                'average_rating' => 0
            ];
        }
    }
    
    /**
     * Get popular categories (sessions per category)
     * 
     * @param int $limit - Number of top categories to return
     * @return array - Categories with session counts
     */
    public static function getPopularCategories($limit = 5) {
        global $conn;
        
        $sql = "SELECT 
                    c.category_id,
                    c.name as category_name,
                    COUNT(s.session_id) as session_count,
                    SUM(CASE WHEN s.status = 'upcoming' THEN 1 ELSE 0 END) as upcoming_count,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as completed_count
                FROM Categories c
                LEFT JOIN skill_sessions s ON c.category_id = s.category_id
                GROUP BY c.category_id, c.name
                HAVING session_count > 0
                ORDER BY session_count DESC
                LIMIT ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getPopularCategories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get session completion rate
     * 
     * @return array - Completion statistics
     */
    public static function getCompletionRate() {
        global $conn;
        
        $sql = "SELECT 
                    COUNT(*) as total_sessions,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
                    SUM(CASE WHEN status = 'upcoming' THEN 1 ELSE 0 END) as upcoming_sessions,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_sessions
                FROM skill_sessions";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total = (int)$stats['total_sessions'];
            $completed = (int)$stats['completed_sessions'];
            $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            
            return [
                'total_sessions' => $total,
                'completed_sessions' => $completed,
                'upcoming_sessions' => (int)$stats['upcoming_sessions'],
                'cancelled_sessions' => (int)$stats['cancelled_sessions'],
                'completion_rate' => $rate
            ];
        } catch (PDOException $e) {
            error_log("Error in getCompletionRate: " . $e->getMessage());
            return [
                'total_sessions' => 0,
                'completed_sessions' => 0,
                'upcoming_sessions' => 0,
                'cancelled_sessions' => 0,
                'completion_rate' => 0
            ];
        }
    }
}
