<?php
/**
 * ImpactFactor Model
 * 
 * Manages sustainability impact factors for different skill categories.
 * Used to calculate CO2 savings on the Impact Dashboard.
 * 
 * Usage:
 *   ImpactFactor::getByCategory('Cooking')
 *   ImpactFactor::getAll()
 *   ImpactFactor::calculateImpact('Programming', 25)
 * 
 * @package SkillShare\Models
 */

require_once __DIR__ . '/../config/database.php';

class ImpactFactor {
    /**
     * Get database connection
     */
    private static function getConnection() {
        global $conn;
        return $conn;
    }

    /**
     * Get all active impact factors
     * 
     * @param bool $includeInactive Include inactive factors
     * @return array Array of impact factor records
     */
    public static function getAll($includeInactive = false) {
        $conn = self::getConnection();
        
        $sql = "
            SELECT 
                id,
                skill_category,
                co2_saved_per_participant_kg,
                source_note,
                is_active,
                updated_by,
                updated_at,
                created_at
            FROM impact_factors
        ";
        
        if (!$includeInactive) {
            $sql .= " WHERE is_active = 1";
        }
        
        $sql .= " ORDER BY skill_category ASC";
        
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get impact factor for a specific skill category
     * 
     * @param string $category Skill category name
     * @return array|null Impact factor record or null if not found
     */
    public static function getByCategory($category) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                id,
                skill_category,
                co2_saved_per_participant_kg,
                source_note,
                is_active,
                updated_by,
                updated_at,
                created_at
            FROM impact_factors
            WHERE skill_category = :category
            AND is_active = 1
            LIMIT 1
        ");
        
        $stmt->execute([':category' => $category]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get impact factor by ID
     * 
     * @param int $id Factor ID
     * @return array|null Impact factor record
     */
    public static function getById($id) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                id,
                skill_category,
                co2_saved_per_participant_kg,
                source_note,
                is_active,
                updated_by,
                updated_at,
                created_at
            FROM impact_factors
            WHERE id = :id
            LIMIT 1
        ");
        
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate CO2 impact for a session
     * 
     * @param string $category Skill category
     * @param int $participants Number of participants
     * @return float|null Total CO2 saved in kg, or null if category not found
     */
    public static function calculateImpact($category, $participants) {
        $factor = self::getByCategory($category);
        
        if (!$factor) {
            return null;
        }
        
        return $factor['co2_saved_per_participant_kg'] * $participants;
    }

    /**
     * Get all unique skill categories
     * 
     * @param bool $activeOnly Only return active categories
     * @return array List of category names
     */
    public static function getCategories($activeOnly = true) {
        $conn = self::getConnection();
        
        $sql = "SELECT skill_category FROM impact_factors";
        
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        
        $sql .= " ORDER BY skill_category ASC";
        
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Create a new impact factor
     * 
     * @param array $data Impact factor data
     * @return int|false New factor ID or false on failure
     */
    public static function create($data) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO impact_factors (
                skill_category,
                co2_saved_per_participant_kg,
                source_note,
                is_active,
                updated_by
            ) VALUES (
                :category,
                :co2_saved,
                :source_note,
                :is_active,
                :updated_by
            )
        ");
        
        $success = $stmt->execute([
            ':category' => $data['skill_category'],
            ':co2_saved' => $data['co2_saved_per_participant_kg'],
            ':source_note' => $data['source_note'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':updated_by' => $data['updated_by'] ?? null
        ]);
        
        return $success ? $conn->lastInsertId() : false;
    }

    /**
     * Update an existing impact factor
     * 
     * @param int $id Factor ID
     * @param array $data Updated data
     * @param int|null $userId User making the update
     * @return bool Success status
     */
    public static function update($id, $data, $userId = null) {
        $conn = self::getConnection();
        
        $fields = [];
        $params = [':id' => $id, ':updated_by' => $userId];
        
        if (isset($data['skill_category'])) {
            $fields[] = "skill_category = :category";
            $params[':category'] = $data['skill_category'];
        }
        
        if (isset($data['co2_saved_per_participant_kg'])) {
            $fields[] = "co2_saved_per_participant_kg = :co2_saved";
            $params[':co2_saved'] = $data['co2_saved_per_participant_kg'];
        }
        
        if (isset($data['source_note'])) {
            $fields[] = "source_note = :source_note";
            $params[':source_note'] = $data['source_note'];
        }
        
        if (isset($data['is_active'])) {
            $fields[] = "is_active = :is_active";
            $params[':is_active'] = $data['is_active'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_by = :updated_by";
        $fields[] = "updated_at = NOW()";
        
        $sql = "UPDATE impact_factors SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Toggle active status of an impact factor
     * 
     * @param int $id Factor ID
     * @param int|null $userId User making the change
     * @return bool Success status
     */
    public static function toggleActive($id, $userId = null) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("
            UPDATE impact_factors 
            SET is_active = NOT is_active,
                updated_by = :user_id,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId
        ]);
    }

    /**
     * Delete an impact factor
     * 
     * @param int $id Factor ID
     * @return bool Success status
     */
    public static function delete($id) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("DELETE FROM impact_factors WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Check if a category exists
     * 
     * @param string $category Category name
     * @param int|null $excludeId Exclude this ID from check (for updates)
     * @return bool
     */
    public static function categoryExists($category, $excludeId = null) {
        $conn = self::getConnection();
        
        $sql = "SELECT COUNT(*) FROM impact_factors WHERE skill_category = :category";
        $params = [':category' => $category];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get total CO2 saved across all sessions (requires Session model integration)
     * This is a helper method for the Impact Dashboard
     * 
     * @return array Statistics [total_kg, total_sessions, total_participants]
     */
    public static function getTotalImpact() {
        $conn = self::getConnection();
        
        $stmt = $conn->query("
            SELECT 
                COUNT(DISTINCT s.id) as total_sessions,
                COALESCE(SUM(
                    (SELECT COUNT(*) FROM bookings b WHERE b.session_id = s.id AND b.status = 'confirmed')
                ), 0) as total_participants,
                COALESCE(SUM(
                    (SELECT COUNT(*) FROM bookings b WHERE b.session_id = s.id AND b.status = 'confirmed') 
                    * IFNULL(impf.co2_saved_per_participant_kg, 0)
                ), 0) as total_co2_saved_kg
            FROM sessions s
            LEFT JOIN impact_factors impf ON s.category = impf.skill_category AND impf.is_active = 1
            WHERE s.status = 'completed'
        ");
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get impact breakdown by category
     * 
     * @return array Impact statistics per category
     */
    public static function getImpactByCategory() {
        $conn = self::getConnection();
        
        $stmt = $conn->query("
            SELECT 
                s.category,
                COUNT(DISTINCT s.id) as session_count,
                COALESCE(SUM(
                    (SELECT COUNT(*) FROM bookings b WHERE b.session_id = s.id AND b.status = 'confirmed')
                ), 0) as participant_count,
                COALESCE(SUM(
                    (SELECT COUNT(*) FROM bookings b WHERE b.session_id = s.id AND b.status = 'confirmed') 
                    * IFNULL(impf.co2_saved_per_participant_kg, 0)
                ), 0) as total_co2_saved_kg,
                impf.co2_saved_per_participant_kg as co2_per_participant
            FROM sessions s
            LEFT JOIN impact_factors impf ON s.category = impf.skill_category AND impf.is_active = 1
            WHERE s.status = 'completed'
            GROUP BY s.category, impf.co2_saved_per_participant_kg
            ORDER BY total_co2_saved_kg DESC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
