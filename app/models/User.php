<?php
/**
 * User Model
 * Handles all database operations related to users
 */

require_once __DIR__ . '/../config/database.php';

class User {
    
    /**
     * Get user by ID
     * 
     * @param int $user_id - User ID
     * @return array|false - User data or false if not found
     */
    public static function getUserById($user_id) {
        global $conn;
        
        $sql = "SELECT user_id, full_name, email, role, city, is_suspended, suspended_reason, created_at, updated_at
                FROM Users 
                WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by email
     * 
     * @param string $email - User email
     * @return array|false - User data or false if not found
     */
    public static function getUserByEmail($email) {
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE email = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUserByEmail: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new user
     * 
     * @param array $data - User data (full_name, email, password_hash, role, city)
     * @return int|false - New user ID or false on failure
     */
    public static function createUser($data) {
        global $conn;
        
        $sql = "INSERT INTO Users (full_name, email, password_hash, role, city) 
                VALUES (?, ?, ?, ?, ?)";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['password_hash'],
                $data['role'],
                $data['city'] ?? null
            ]);
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in createUser: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user profile
     * 
     * @param int $user_id - User ID
     * @param array $data - Data to update
     * @return bool - Success status
     */
    public static function updateUser($user_id, $data) {
        global $conn;
        
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $user_id;
        $sql = "UPDATE Users SET " . implode(', ', $fields) . " WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error in updateUser: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Suspend user account
     * 
     * @param int $user_id - User ID
     * @param string $reason - Suspension reason
     * @return bool - Success status
     */
    public static function suspendUser($user_id, $reason) {
        global $conn;
        
        $sql = "UPDATE Users SET is_suspended = TRUE, suspended_reason = ? WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$reason, $user_id]);
        } catch (PDOException $e) {
            error_log("Error in suspendUser: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Unsuspend user account
     * 
     * @param int $user_id - User ID
     * @return bool - Success status
     */
    public static function unsuspendUser($user_id) {
        global $conn;
        
        $sql = "UPDATE Users SET is_suspended = FALSE, suspended_reason = NULL WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error in unsuspendUser: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users by role
     * 
     * @param string $role - User role (instructor, learner, admin)
     * @return array|false - Array of users or false on failure
     */
    public static function getUsersByRole($role) {
        global $conn;
        
        $sql = "SELECT user_id, full_name, email, role, city, is_suspended, created_at
                FROM Users 
                WHERE role = ?
                ORDER BY created_at DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$role]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUsersByRole: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email - Email to check
     * @param int $exclude_user_id - User ID to exclude from check (for updates)
     * @return bool - True if email exists
     */
    public static function emailExists($email, $exclude_user_id = null) {
        global $conn;
        
        $sql = "SELECT COUNT(*) as count FROM Users WHERE email = ?";
        $params = [$email];
        
        if ($exclude_user_id) {
            $sql .= " AND user_id != ?";
            $params[] = $exclude_user_id;
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error in emailExists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by ID (alias for getUserById)
     * 
     * @param int $id - User ID
     * @return array|false - User data or false if not found
     */
    public static function getById($id) {
        return self::getUserById($id);
    }
    
    /**
     * Update user profile information
     * 
     * @param int $userId - User ID
     * @param array $data - Data to update (full_name, email, city)
     * @return bool - Success status
     */
    public static function update($userId, $data) {
        global $conn;
        
        $fields = [];
        $params = [];
        
        if (isset($data['full_name'])) {
            $fields[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            // Check if email is already taken by another user
            if (self::emailExists($data['email'], $userId)) {
                return false;
            }
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['city'])) {
            $fields[] = "city = ?";
            $params[] = $data['city'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $userId;
        $sql = "UPDATE Users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error in User::update: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify user's current password
     * 
     * @param int $userId - User ID
     * @param string $password - Password to verify
     * @return bool - True if password is correct
     */
    public static function verifyPassword($userId, $password) {
        global $conn;
        
        $sql = "SELECT password_hash FROM Users WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error in verifyPassword: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user password
     * 
     * @param int $userId - User ID
     * @param string $newPassword - New password (plain text, will be hashed)
     * @return bool - Success status
     */
    public static function updatePassword($userId, $newPassword) {
        global $conn;
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET password_hash = ?, updated_at = NOW() WHERE user_id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$passwordHash, $userId]);
        } catch (PDOException $e) {
            error_log("Error in updatePassword: " . $e->getMessage());
            return false;
        }
    }
}
