<?php
/**
 * Settings Model
 * 
 * Manages application settings stored in the database.
 * Implements caching, type casting, and access control for settings.
 * 
 * Usage:
 *   Settings::get('security.session_timeout_minutes')  // Returns int 30
 *   Settings::get('booking.currency')                   // Returns string 'GBP'
 *   Settings::getPublic()                               // Returns only public settings
 *   Settings::update('ui.sessions_per_page', 15, $userId)
 * 
 * @package SkillShare\Models
 */

require_once __DIR__ . '/../config/database.php';

class Settings {
    private static $cache = null;
    private static $cacheTimestamp = null;
    private static $cacheDuration = 300; // 5 minutes in seconds

    /**
     * Get database connection
     */
    private static function getConnection() {
        global $conn;
        return $conn;
    }

    /**
     * Load all settings from database into cache
     * 
     * @return array Associative array of settings [key => [value, type, ...]]
     */
    private static function loadCache() {
        $conn = self::getConnection();
        
        $stmt = $conn->query("
            SELECT 
                setting_key, 
                setting_value, 
                value_type, 
                group_name, 
                description, 
                is_public, 
                is_editable,
                validation_rules,
                default_value,
                updated_by,
                updated_at
            FROM app_settings
            ORDER BY group_name, setting_key
        ");
        
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = [
                'value' => self::castValue($row['setting_value'], $row['value_type']),
                'raw_value' => $row['setting_value'],
                'type' => $row['value_type'],
                'group' => $row['group_name'],
                'description' => $row['description'],
                'is_public' => (bool)$row['is_public'],
                'is_editable' => (bool)$row['is_editable'],
                'validation_rules' => $row['validation_rules'] ? json_decode($row['validation_rules'], true) : null,
                'default_value' => $row['default_value'],
                'updated_by' => $row['updated_by'],
                'updated_at' => $row['updated_at']
            ];
        }
        
        self::$cache = $settings;
        self::$cacheTimestamp = time();
        
        return $settings;
    }

    /**
     * Check if cache is still valid
     * 
     * @return bool
     */
    private static function isCacheValid() {
        if (self::$cache === null || self::$cacheTimestamp === null) {
            return false;
        }
        
        return (time() - self::$cacheTimestamp) < self::$cacheDuration;
    }

    /**
     * Get all settings (refreshes cache if needed)
     * 
     * @return array
     */
    private static function getAll() {
        if (!self::isCacheValid()) {
            self::loadCache();
        }
        
        return self::$cache;
    }

    /**
     * Cast a setting value to its appropriate type
     * 
     * @param string $value The raw value from database
     * @param string $type The target type (string, int, float, bool, json)
     * @return mixed The cast value
     */
    private static function castValue($value, $type) {
        switch ($type) {
            case 'int':
                return (int)$value;
            
            case 'float':
                return (float)$value;
            
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            
            case 'json':
                $decoded = json_decode($value, true);
                return ($decoded !== null) ? $decoded : [];
            
            case 'string':
            default:
                return (string)$value;
        }
    }

    /**
     * Get a single setting value by key
     * 
     * @param string $key Dot notation key (e.g., 'security.session_timeout_minutes')
     * @param mixed $default Default value if setting not found
     * @return mixed The cast setting value or default
     */
    public static function get($key, $default = null) {
        $settings = self::getAll();
        
        if (!isset($settings[$key])) {
            return $default;
        }
        
        return $settings[$key]['value'];
    }

    /**
     * Get raw (uncast) setting value
     * 
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return string|null
     */
    public static function getRaw($key, $default = null) {
        $settings = self::getAll();
        
        if (!isset($settings[$key])) {
            return $default;
        }
        
        return $settings[$key]['raw_value'];
    }

    /**
     * Get all settings in a specific group
     * 
     * @param string $group Group name (e.g., 'security', 'booking')
     * @return array Associative array of settings in the group
     */
    public static function getGroup($group) {
        $settings = self::getAll();
        $result = [];
        
        foreach ($settings as $key => $data) {
            if ($data['group'] === $group) {
                $result[$key] = $data['value'];
            }
        }
        
        return $result;
    }

    /**
     * Get only public settings (safe to expose to frontend)
     * 
     * @return array Associative array [key => value] of public settings
     */
    public static function getPublic() {
        $settings = self::getAll();
        $result = [];
        
        foreach ($settings as $key => $data) {
            if ($data['is_public']) {
                $result[$key] = $data['value'];
            }
        }
        
        return $result;
    }

    /**
     * Get all settings with full metadata (for admin UI)
     * 
     * @param bool $editableOnly Return only editable settings
     * @return array Full settings data
     */
    public static function getAllWithMetadata($editableOnly = false) {
        $settings = self::getAll();
        
        if ($editableOnly) {
            return array_filter($settings, function($data) {
                return $data['is_editable'];
            });
        }
        
        return $settings;
    }

    /**
     * Update a setting value
     * 
     * @param string $key Setting key
     * @param mixed $value New value
     * @param int|null $userId ID of user making the change
     * @return bool Success status
     */
    public static function update($key, $value, $userId = null) {
        $conn = self::getConnection();
        
        // Check if setting exists and is editable
        $settings = self::getAll();
        if (!isset($settings[$key])) {
            return false;
        }
        
        if (!$settings[$key]['is_editable']) {
            return false; // Cannot edit non-editable settings
        }
        
        // Validate the value
        if (!self::validate($key, $value)) {
            return false;
        }
        
        // Convert value to string for storage
        $type = $settings[$key]['type'];
        if ($type === 'json') {
            $valueStr = json_encode($value);
        } elseif ($type === 'bool') {
            $valueStr = $value ? 'true' : 'false';
        } else {
            $valueStr = (string)$value;
        }
        
        // Update in database
        $stmt = $conn->prepare("
            UPDATE app_settings 
            SET setting_value = :value,
                updated_by = :user_id,
                updated_at = NOW()
            WHERE setting_key = :key
        ");
        
        $success = $stmt->execute([
            ':value' => $valueStr,
            ':user_id' => $userId,
            ':key' => $key
        ]);
        
        // Invalidate cache
        if ($success) {
            self::clearCache();
        }
        
        return $success;
    }

    /**
     * Update multiple settings at once
     * 
     * @param array $settings Associative array [key => value]
     * @param int|null $userId ID of user making changes
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public static function updateBatch($settings, $userId = null) {
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($settings as $key => $value) {
            if (self::update($key, $value, $userId)) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Failed to update: $key";
            }
        }
        
        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    /**
     * Clear the settings cache (force reload on next access)
     */
    public static function clearCache() {
        self::$cache = null;
        self::$cacheTimestamp = null;
    }

    /**
     * Check if a specific setting exists
     * 
     * @param string $key Setting key
     * @return bool
     */
    public static function exists($key) {
        $settings = self::getAll();
        return isset($settings[$key]);
    }

    /**
     * Get setting metadata without the value
     * 
     * @param string $key Setting key
     * @return array|null Metadata or null if not found
     */
    public static function getMetadata($key) {
        $settings = self::getAll();
        
        if (!isset($settings[$key])) {
            return null;
        }
        
        $data = $settings[$key];
        unset($data['value']); // Remove the actual value
        
        return $data;
    }

    /**
     * Get all unique group names
     * 
     * @return array List of group names
     */
    public static function getGroups() {
        $settings = self::getAll();
        $groups = array_unique(array_column($settings, 'group'));
        sort($groups);
        return $groups;
    }

    /**
     * Create a new setting (for dynamic settings)
     * 
     * @param array $data Setting data [key, value, type, group, description, etc.]
     * @return bool Success status
     */
    public static function create($data) {
        $conn = self::getConnection();
        
        $required = ['setting_key', 'setting_value', 'value_type', 'group_name', 'description'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        
        $stmt = $conn->prepare("
            INSERT INTO app_settings (
                setting_key, setting_value, value_type, group_name, description,
                is_public, is_editable, validation_rules, default_value, updated_by
            ) VALUES (
                :key, :value, :type, :group, :description,
                :is_public, :is_editable, :validation_rules, :default_value, :updated_by
            )
        ");
        
        $success = $stmt->execute([
            ':key' => $data['setting_key'],
            ':value' => $data['setting_value'],
            ':type' => $data['value_type'],
            ':group' => $data['group_name'],
            ':description' => $data['description'],
            ':is_public' => $data['is_public'] ?? 0,
            ':is_editable' => $data['is_editable'] ?? 1,
            ':validation_rules' => isset($data['validation_rules']) ? json_encode($data['validation_rules']) : null,
            ':default_value' => $data['default_value'] ?? $data['setting_value'],
            ':updated_by' => $data['updated_by'] ?? null
        ]);
        
        if ($success) {
            self::clearCache();
        }
        
        return $success;
    }

    /**
     * Validate a setting value against its validation rules
     * 
     * @param string $key Setting key
     * @param mixed $value Value to validate
     * @return bool True if valid or no rules, false otherwise
     */
    public static function validate($key, $value) {
        $settings = self::getAll();
        
        if (!isset($settings[$key])) {
            return false;
        }
        
        $rules = $settings[$key]['validation_rules'];
        
        // No validation rules = always valid
        if (!$rules || !is_array($rules)) {
            return true;
        }
        
        $type = $settings[$key]['type'];
        
        // Min/Max validation for numbers
        if (in_array($type, ['int', 'float'])) {
            if (isset($rules['min']) && $value < $rules['min']) {
                return false;
            }
            if (isset($rules['max']) && $value > $rules['max']) {
                return false;
            }
        }
        
        // Min/Max length for strings
        if ($type === 'string') {
            if (isset($rules['min_length']) && strlen($value) < $rules['min_length']) {
                return false;
            }
            if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
                return false;
            }
            
            // Regex validation
            if (isset($rules['regex']) && !preg_match($rules['regex'], $value)) {
                return false;
            }
        }
        
        // Enum validation (allowed values)
        if (isset($rules['enum']) && is_array($rules['enum'])) {
            if (!in_array($value, $rules['enum'])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Reset a setting to its default value
     * 
     * @param string $key Setting key
     * @param int|null $userId ID of user making the change
     * @return bool Success status
     */
    public static function reset($key, $userId = null) {
        $conn = self::getConnection();
        
        // Get the default value
        $settings = self::getAll();
        if (!isset($settings[$key])) {
            return false;
        }
        
        $defaultValue = $settings[$key]['default_value'];
        if ($defaultValue === null) {
            return false; // No default value defined
        }
        
        // Update to default value
        $stmt = $conn->prepare("
            UPDATE app_settings 
            SET setting_value = :value,
                updated_by = :user_id,
                updated_at = NOW()
            WHERE setting_key = :key
        ");
        
        $success = $stmt->execute([
            ':value' => $defaultValue,
            ':user_id' => $userId,
            ':key' => $key
        ]);
        
        if ($success) {
            self::clearCache();
        }
        
        return $success;
    }

    /**
     * Reset all settings in a group to their default values
     * 
     * @param string $group Group name
     * @param int|null $userId ID of user making the change
     * @return array ['success' => int, 'failed' => int]
     */
    public static function resetGroup($group, $userId = null) {
        $settings = self::getGroup($group);
        $success = 0;
        $failed = 0;
        
        foreach (array_keys($settings) as $key) {
            if (self::reset($key, $userId)) {
                $success++;
            } else {
                $failed++;
            }
        }
        
        return [
            'success' => $success,
            'failed' => $failed
        ];
    }

    /**
     * Get the default value for a setting
     * 
     * @param string $key Setting key
     * @return mixed|null Default value or null if not defined
     */
    public static function getDefault($key) {
        $settings = self::getAll();
        
        if (!isset($settings[$key])) {
            return null;
        }
        
        $defaultValue = $settings[$key]['default_value'];
        $type = $settings[$key]['type'];
        
        if ($defaultValue === null) {
            return null;
        }
        
        return self::castValue($defaultValue, $type);
    }

    /**
     * Get validation rules for a setting
     * 
     * @param string $key Setting key
     * @return array|null Validation rules or null
     */
    public static function getValidationRules($key) {
        $settings = self::getAll();
        
        if (!isset($settings[$key])) {
            return null;
        }
        
        return $settings[$key]['validation_rules'];
    }

    /**
     * Get settings organized by group (for admin UI)
     * 
     * @param bool $editableOnly Return only editable settings
     * @return array Settings grouped by group_name
     */
    public static function getGrouped($editableOnly = false) {
        $settings = self::getAllWithMetadata($editableOnly);
        $grouped = [];
        
        foreach ($settings as $key => $data) {
            $group = $data['group'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][$key] = $data;
        }
        
        // Sort groups alphabetically
        ksort($grouped);
        
        return $grouped;
    }
}
