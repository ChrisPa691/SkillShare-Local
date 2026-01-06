<?php
/**
 * Rating Model
 * Handles all database operations related to session ratings
 */

require_once __DIR__ . '/../config/database.php';

class Rating {
    
    /**
     * Get all ratings for a specific session
     * 
     * @param int $session_id - Session ID
     * @return array|false - Array of ratings or false on failure
     */
    public static function getRatingsBySession($session_id) {
        global $conn;
        
        $sql = "SELECT r.*, 
                       u.full_name as learner_name
                FROM ratings r
                INNER JOIN Users u ON r.learner_id = u.user_id
                WHERE r.session_id = ?
                ORDER BY r.created_at DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$session_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getRatingsBySession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get rating by learner and session
     * 
     * @param int $learner_id - Learner ID
     * @param int $session_id - Session ID
     * @return array|false - Rating data or false if not found
     */
    public static function getRatingByLearnerAndSession($learner_id, $session_id) {
        global $conn;
        
        $sql = "SELECT * FROM ratings WHERE learner_id = ? AND session_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$learner_id, $session_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getRatingByLearnerAndSession: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new rating
     * 
     * @param array $data - Rating data (session_id, learner_id, rating, comment)
     * @return int|false - New rating ID or false on failure
     */
    public static function createRating($data) {
        global $conn;
        
        $sql = "INSERT INTO ratings (session_id, learner_id, rating, comment) 
                VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data['session_id'],
                $data['learner_id'],
                $data['rating'],
                $data['comment'] ?? null
            ]);
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in createRating: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing rating
     * 
     * @param int $rating_id - Rating ID
     * @param array $data - Updated rating data
     * @return bool - Success status
     */
    public static function updateRating($rating_id, $data) {
        global $conn;
        
        $sql = "UPDATE ratings SET rating = ?, comment = ? WHERE rating_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([
                $data['rating'],
                $data['comment'] ?? null,
                $rating_id
            ]);
        } catch (PDOException $e) {
            error_log("Error in updateRating: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a rating
     * 
     * @param int $rating_id - Rating ID
     * @return bool - Success status
     */
    public static function deleteRating($rating_id) {
        global $conn;
        
        $sql = "DELETE FROM ratings WHERE rating_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$rating_id]);
        } catch (PDOException $e) {
            error_log("Error in deleteRating: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get average rating for a session
     * 
     * @param int $session_id - Session ID
     * @return float - Average rating
     */
    public static function getAverageRating($session_id) {
        global $conn;
        
        $sql = "SELECT COALESCE(AVG(rating), 0) as average_rating 
                FROM ratings 
                WHERE session_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$session_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return floatval($result['average_rating']);
        } catch (PDOException $e) {
            error_log("Error in getAverageRating: " . $e->getMessage());
            return 0.0;
        }
    }
    
    /**
     * Get ratings by learner
     * 
     * @param int $learner_id - Learner ID
     * @return array|false - Array of ratings or false on failure
     */
    public static function getRatingsByLearner($learner_id) {
        global $conn;
        
        $sql = "SELECT r.*, 
                       s.title as session_title,
                       s.event_datetime
                FROM ratings r
                INNER JOIN skill_sessions s ON r.session_id = s.session_id
                WHERE r.learner_id = ?
                ORDER BY r.created_at DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$learner_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getRatingsByLearner: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get platform-wide average rating statistics
     * 
     * @return array - Rating statistics
     */
    public static function getPlatformRatingStats() {
        global $conn;
        
        $sql = "SELECT 
                    COALESCE(AVG(rating), 0) as average_rating,
                    COUNT(*) as total_ratings,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM ratings";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'average_rating' => round((float)$stats['average_rating'], 2),
                'total_ratings' => (int)$stats['total_ratings'],
                'five_stars' => (int)$stats['five_stars'],
                'four_stars' => (int)$stats['four_stars'],
                'three_stars' => (int)$stats['three_stars'],
                'two_stars' => (int)$stats['two_stars'],
                'one_star' => (int)$stats['one_star']
            ];
        } catch (PDOException $e) {
            error_log("Error in getPlatformRatingStats: " . $e->getMessage());
            return [
                'average_rating' => 0,
                'total_ratings' => 0,
                'five_stars' => 0,
                'four_stars' => 0,
                'three_stars' => 0,
                'two_stars' => 0,
                'one_star' => 0
            ];
        }
    }
}
