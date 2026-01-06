<?php
/**
 * UserSettings Model
 * 
 * Manages user-specific settings stored in user_settings table.
 * 
 * @package SkillShare\Models
 */

require_once __DIR__ . '/../config/database.php';

class UserSettings {
    
    /**
     * Get database connection
     */
    private static function getConnection() {
        global $conn;
        return $conn;
    }

    /**
     * Get user settings by user ID
     * 
     * @param int $userId User ID
     * @return array|null User settings or null if not found
     */
    public static function get($userId) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                theme, 
                font_size, 
                line_height, 
                contrast_mode,
                language,
                timezone,
                currency,
                notify_email,
                notify_inapp,
                notify_push,
                notify_events,
                created_at,
                updated_at
            FROM user_settings
            WHERE user_id = :user_id
        ");
        
        $stmt->execute(['user_id' => $userId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($settings) {
            // Decode JSON fields
            if (isset($settings['notify_events'])) {
                $settings['notify_events'] = json_decode($settings['notify_events'], true);
            }
            
            // Convert TINYINT to boolean
            $settings['notify_email'] = (bool)$settings['notify_email'];
            $settings['notify_inapp'] = (bool)$settings['notify_inapp'];
            $settings['notify_push'] = (bool)$settings['notify_push'];
        }
        
        return $settings;
    }

    /**
     * Get a specific setting value
     * 
     * @param int $userId User ID
     * @param string $key Setting key (theme, language, etc.)
     * @param mixed $default Default value if not found
     * @return mixed Setting value or default
     */
    public static function getValue($userId, $key, $default = null) {
        $settings = self::get($userId);
        
        if ($settings === null) {
            return $default;
        }
        
        return $settings[$key] ?? $default;
    }

    /**
     * Create default settings for a user
     * 
     * @param int $userId User ID
     * @return bool Success
     */
    public static function create($userId) {
        $conn = self::getConnection();
        
        try {
            $stmt = $conn->prepare("
                INSERT INTO user_settings (user_id)
                VALUES (:user_id)
            ");
            
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            // If duplicate, it's okay
            if ($e->getCode() == 23000) {
                return true;
            }
            throw $e;
        }
    }

    /**
     * Update user settings
     * 
     * @param int $userId User ID
     * @param array $data Associative array of settings to update
     * @return bool Success
     */
    public static function update($userId, $data) {
        $conn = self::getConnection();
        
        // Ensure user settings exist
        self::create($userId);
        
        // Build UPDATE query dynamically
        $allowedFields = [
            'theme', 'font_size', 'line_height', 'contrast_mode',
            'language', 'timezone', 'currency',
            'notify_email', 'notify_inapp', 'notify_push', 'notify_events'
        ];
        
        $updates = [];
        $params = ['user_id' => $userId];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                // Handle JSON fields
                if ($key === 'notify_events' && is_array($value)) {
                    $value = json_encode($value);
                }
                
                // Handle boolean fields
                if (in_array($key, ['notify_email', 'notify_inapp', 'notify_push'])) {
                    $value = $value ? 1 : 0;
                }
                
                $updates[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE user_settings SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update a single setting
     * 
     * @param int $userId User ID
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool Success
     */
    public static function updateSingle($userId, $key, $value) {
        return self::update($userId, [$key => $value]);
    }

    /**
     * Delete user settings
     * 
     * @param int $userId User ID
     * @return bool Success
     */
    public static function delete($userId) {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("DELETE FROM user_settings WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Reset user settings to defaults
     * 
     * @param int $userId User ID
     * @return bool Success
     */
    public static function reset($userId) {
        // Delete and recreate
        self::delete($userId);
        return self::create($userId);
    }

    /**
     * Get all users with custom settings
     * 
     * @return array Array of user IDs
     */
    public static function getUsersWithSettings() {
        $conn = self::getConnection();
        
        $stmt = $conn->query("SELECT user_id FROM user_settings ORDER BY user_id");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Initialize settings for all users who don't have them
     * 
     * @return int Number of users initialized
     */
    public static function initializeAllUsers() {
        $conn = self::getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO user_settings (user_id)
            SELECT user_id FROM Users
            WHERE user_id NOT IN (SELECT user_id FROM user_settings)
        ");
        
        $stmt->execute();
        return $stmt->rowCount();
    }
}
