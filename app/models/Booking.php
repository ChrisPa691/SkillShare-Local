<?php
/**
 * Booking Model
 * Handles all database operations related to session bookings
 */

require_once __DIR__ . '/../config/database.php';

class Booking {
    
    /**
     * Get booking by learner and session
     * 
     * @param int $learner_id - Learner ID
     * @param int $session_id - Session ID
     * @return array|false - Booking data or false if not found
     */
    public static function getBookingByLearnerAndSession($learner_id, $session_id) {
        global $conn;
        
        $sql = "SELECT * FROM bookings WHERE learner_id = ? AND session_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$learner_id, $session_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getBookingByLearnerAndSession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get booking by ID
     * 
     * @param int $booking_id - Booking ID
     * @return array|false - Booking data or false if not found
     */
    public static function getBookingById($booking_id) {
        global $conn;
        
        $sql = "SELECT b.*, 
                       s.title as session_title,
                       s.event_datetime,
                       s.location_type,
                       s.city,
                       s.address,
                       s.online_link,
                       u.full_name as instructor_name
                FROM bookings b
                INNER JOIN skill_sessions s ON b.session_id = s.session_id
                INNER JOIN Users u ON s.instructor_id = u.user_id
                WHERE b.booking_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$booking_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getBookingById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all bookings for a learner
     * 
     * @param int $learner_id - Learner ID
     * @param string $status - Filter by status (optional)
     * @return array|false - Array of bookings or false on failure
     */
    public static function getBookingsByLearner($learner_id, $status = null) {
        global $conn;
        
        $sql = "SELECT b.*, 
                       s.title as session_title,
                       s.event_datetime,
                       s.duration_minutes,
                       s.location_type,
                       s.city,
                       s.address,
                       s.online_link,
                       s.status as session_status,
                       u.full_name as instructor_name
                FROM bookings b
                INNER JOIN skill_sessions s ON b.session_id = s.session_id
                INNER JOIN Users u ON s.instructor_id = u.user_id
                WHERE b.learner_id = ?";
        
        $params = [$learner_id];
        
        if ($status !== null) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY s.event_datetime DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getBookingsByLearner: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all bookings for a session
     * 
     * @param int $session_id - Session ID
     * @param string $status - Filter by status (optional)
     * @return array|false - Array of bookings or false on failure
     */
    public static function getBookingsBySession($session_id, $status = null) {
        global $conn;
        
        $sql = "SELECT b.*, 
                       u.full_name as learner_name,
                       u.email as learner_email,
                       s.title as session_title,
                       s.event_datetime
                FROM bookings b
                INNER JOIN Users u ON b.learner_id = u.user_id
                INNER JOIN skill_sessions s ON b.session_id = s.session_id
                WHERE b.session_id = ?";
        
        $params = [$session_id];
        
        if ($status !== null) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.requested_at DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getBookingsBySession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new booking
     * 
     * @param array $data - Booking data (session_id, learner_id)
     * @return int|false - New booking ID or false on failure
     */
    public static function createBooking($data) {
        global $conn;
        
        $num_seats = $data['num_seats'] ?? 1;
        
        $sql = "INSERT INTO bookings (session_id, learner_id, num_seats, status, requested_at) 
                VALUES (?, ?, ?, 'pending', NOW())";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data['session_id'],
                $data['learner_id'],
                $num_seats
            ]);
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in createBooking: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update booking status
     * 
     * @param int $booking_id - Booking ID
     * @param string $status - New status (pending, accepted, declined, canceled)
     * @return bool - Success status
     */
    public static function updateBookingStatus($booking_id, $status) {
        global $conn;
        
        $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$status, $booking_id]);
        } catch (PDOException $e) {
            error_log("Error in updateBookingStatus: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cancel a booking
     * 
     * @param int $booking_id - Booking ID
     * @return bool - Success status
     */
    public static function cancelBooking($booking_id) {
        return self::updateBookingStatus($booking_id, 'canceled');
    }
    
    /**
     * Accept a booking
     * 
     * @param int $booking_id - Booking ID
     * @return bool - Success status
     */
    public static function acceptBooking($booking_id) {
        return self::updateBookingStatus($booking_id, 'accepted');
    }
    
    /**
     * Delete a booking
     * 
     * @param int $booking_id - Booking ID
     * @return bool - Success status
     */
    public static function deleteBooking($booking_id) {
        global $conn;
        
        $sql = "DELETE FROM bookings WHERE booking_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$booking_id]);
        } catch (PDOException $e) {
            error_log("Error in deleteBooking: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get booking statistics for an instructor
     * 
     * @param int $instructor_id - Instructor ID
     * @return array - Booking statistics
     */
    public static function getInstructorBookingStats($instructor_id) {
        global $conn;
        
        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN b.status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN b.status = 'accepted' THEN 1 ELSE 0 END) as accepted_bookings,
                    SUM(CASE WHEN b.status = 'declined' THEN 1 ELSE 0 END) as declined_bookings
                FROM bookings b
                INNER JOIN skill_sessions s ON b.session_id = s.session_id
                WHERE s.instructor_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$instructor_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getInstructorBookingStats: " . $e->getMessage());
            return [
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'accepted_bookings' => 0,
                'declined_bookings' => 0
            ];
        }
    }
    
    /**
     * Get bookings per month statistics
     * 
     * @param int $limit - Number of months to fetch (default: 12)
     * @return array - Monthly booking statistics
     */
    public static function getBookingsPerMonth($limit = 12) {
        global $conn;
        
        $sql = "SELECT 
                    DATE_FORMAT(requested_at, '%Y-%m') as month,
                    DATE_FORMAT(requested_at, '%M %Y') as month_label,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined,
                    SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled
                FROM bookings
                WHERE requested_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(requested_at, '%Y-%m')
                ORDER BY month DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getBookingsPerMonth: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get overall booking statistics
     * 
     * @return array - Overall booking statistics
     */
    public static function getOverallStats() {
        global $conn;
        
        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined,
                    SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled
                FROM bookings";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getOverallStats: " . $e->getMessage());
            return [
                'total_bookings' => 0,
                'pending' => 0,
                'accepted' => 0,
                'declined' => 0,
                'canceled' => 0
            ];
        }
    }
}
