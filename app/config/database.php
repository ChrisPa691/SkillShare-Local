<?php
/**
 * Database Connection and CRUD Functions - PDO Version
 * Simple university-level database operations
 */

require_once __DIR__ . '/config.php';

// Use the global $conn from config.php
global $conn;

/**
 * INSERT - Create a new record
 * 
 * @param string $table - Table name
 * @param array $data - Associative array of column => value
 * @return int|false - Last inserted ID or false on failure
 */
function db_insert($table, $data) {
    global $conn;
    
    try {
        // Prepare column names and placeholders
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        error_log("DB Insert Error: " . $e->getMessage());
        return false;
    }
}

/**
 * SELECT - Read records from database
 * 
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions (column => value)
 * @param int $limit - Limit number of results (0 = no limit)
 * @param string $columns - Columns to select (default: *)
 * @return array|false - Array of results or false on failure
 */
function db_select($table, $conditions = [], $limit = 0, $columns = '*') {
    global $conn;
    
    try {
        $sql = "SELECT $columns FROM $table";
        
        // Add WHERE clause if conditions exist
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = :$column";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        // Add LIMIT clause if specified
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
        }
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters if conditions exist
        if (!empty($conditions)) {
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB Select Error: " . $e->getMessage());
        return false;
    }
}

/**
 * SELECT ONE - Read single record
 * 
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions
 * @param string $columns - Columns to select
 * @return array|false - Single record or false
 */
function db_select_one($table, $conditions, $columns = '*') {
    $results = db_select($table, $conditions, $columns);
    return !empty($results) ? $results[0] : false;
}

/**
 * UPDATE - Update existing records
 * 
 * @param string $table - Table name
 * @param array $data - Data to update (column => value)
 * @param array $conditions - WHERE conditions (column => value)
 * @return bool - True on success, false on failure
 */
function db_update($table, $data, $conditions) {
    global $conn;
    
    try {
        // Prepare SET clause
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = :set_$column";
        }
        
        // Prepare WHERE clause
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = :where_$column";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
        $stmt = $conn->prepare($sql);
        
        // Bind SET parameters
        foreach ($data as $column => $value) {
            $stmt->bindValue(":set_$column", $value);
        }
        
        // Bind WHERE parameters
        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":where_$column", $value);
        }
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("DB Update Error: " . $e->getMessage());
        return false;
    }
}

/**
 * DELETE - Delete records
 * 
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions (column => value)
 * @return bool - True on success, false on failure
 */
function db_delete($table, $conditions) {
    global $conn;
    
    try {
        // Prepare WHERE clause
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = :$column";
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $where);
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("DB Delete Error: " . $e->getMessage());
        return false;
    }
}

/**
 * QUERY - Execute custom SQL query
 * 
 * @param string $sql - SQL query
 * @param array $params - Parameters for prepared statement (key => value)
 * @return array|bool - Results for SELECT, true/false for others
 */
function db_query($sql, $params = []) {
    global $conn;
    
    try {
        $stmt = $conn->prepare($sql);
        
        // Bind parameters if provided
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $param_key = is_numeric($key) ? $key + 1 : $key;
                $stmt->bindValue($param_key, $value);
            }
        }
        
        $stmt->execute();
        
        // Check if query is a SELECT statement
        if (stripos(trim($sql), 'SELECT') === 0) {
            return $stmt->fetchAll();
        }
        
        // For INSERT, UPDATE, DELETE
        return true;
    } catch (PDOException $e) {
        error_log("DB Query Error: " . $e->getMessage());
        return false;
    }
}

/**
 * COUNT - Count records
 * 
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions
 * @return int - Number of records
 */
function db_count($table, $conditions = []) {
    global $conn;
    
    try {
        $sql = "SELECT COUNT(*) as count FROM $table";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = :$column";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        
        return (int)$row['count'];
    } catch (PDOException $e) {
        error_log("DB Count Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Close database connection
 */
function db_close() {
    global $conn;
    $conn = null;
}

?>
