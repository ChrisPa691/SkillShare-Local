<?php
/**
 * Impact Model
 * Handles sustainability impact calculations and tracking
 */

require_once __DIR__ . '/../config/database.php';

class Impact {
    
    /**
     * Get total CO2 savings across all sessions
     * 
     * @return float - Total CO2 saved in kg
     */
    public static function getTotalCO2Saved() {
        global $conn;
        
        try {
            $sql = "SELECT SUM(
                        (s.total_capacity - s.capacity_remaining) * COALESCE(i.co2_saved_per_participant_kg, 0)
                    ) as total_co2_saved
                    FROM skill_sessions s
                    LEFT JOIN impact_factors i ON s.category_id = i.category_id
                    WHERE s.status = 'completed'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return floatval($result['total_co2_saved'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error in getTotalCO2Saved: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get CO2 savings by category
     * 
     * @return array - Array of category impact data
     */
    public static function getCO2SavedByCategory() {
        global $conn;
        
        try {
            $sql = "SELECT c.name as category_name,
                           SUM((s.total_capacity - s.capacity_remaining) * COALESCE(i.co2_saved_per_participant_kg, 0)) as co2_saved,
                           COUNT(s.session_id) as session_count,
                           SUM(s.total_capacity - s.capacity_remaining) as total_participants
                    FROM Categories c
                    LEFT JOIN skill_sessions s ON c.category_id = s.category_id AND s.status = 'completed'
                    LEFT JOIN impact_factors i ON c.category_id = i.category_id
                    GROUP BY c.category_id, c.name
                    HAVING session_count > 0
                    ORDER BY co2_saved DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCO2SavedByCategory: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get impact statistics for a specific instructor
     * 
     * @param int $instructor_id - Instructor ID
     * @return array - Impact statistics
     */
    public static function getInstructorImpact($instructor_id) {
        global $conn;
        
        try {
            $sql = "SELECT 
                        COUNT(s.session_id) as total_sessions,
                        SUM(s.total_capacity - s.capacity_remaining) as total_learners,
                        SUM((s.total_capacity - s.capacity_remaining) * COALESCE(i.co2_saved_per_participant_kg, 0)) as total_co2_saved
                    FROM skill_sessions s
                    LEFT JOIN impact_factors i ON s.category_id = i.category_id
                    WHERE s.instructor_id = ? AND s.status = 'completed'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$instructor_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getInstructorImpact: " . $e->getMessage());
            return [
                'total_sessions' => 0,
                'total_learners' => 0,
                'total_co2_saved' => 0
            ];
        }
    }
    
    /**
     * Get impact factor for a category
     * 
     * @param int $category_id - Category ID
     * @return float - CO2 saved per participant in kg
     */
    public static function getImpactFactor($category_id) {
        global $conn;
        
        try {
            $sql = "SELECT co2_saved_per_participant_kg 
                    FROM impact_factors 
                    WHERE category_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$category_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return floatval($result['co2_saved_per_participant_kg'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error in getImpactFactor: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get overall platform statistics
     * 
     * @return array - Platform-wide statistics
     */
    public static function getPlatformStats() {
        global $conn;
        
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT s.session_id) as total_sessions,
                        COUNT(DISTINCT s.instructor_id) as total_instructors,
                        COUNT(DISTINCT b.learner_id) as total_learners,
                        SUM((s.total_capacity - s.capacity_remaining) * COALESCE(i.co2_saved_per_participant_kg, 0)) as total_co2_saved
                    FROM skill_sessions s
                    LEFT JOIN bookings b ON s.session_id = b.session_id AND b.status = 'accepted'
                    LEFT JOIN impact_factors i ON s.category_id = i.category_id
                    WHERE s.status = 'completed'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getPlatformStats: " . $e->getMessage());
            return [
                'total_sessions' => 0,
                'total_instructors' => 0,
                'total_learners' => 0,
                'total_co2_saved' => 0
            ];
        }
    }
}
