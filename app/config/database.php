<?php
/**
 * Database Connection and CRUD Functions
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
    
    // Prepare column names and values
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Bind parameters dynamically
    $types = str_repeat('s', count($data)); // All strings for simplicity
    $values = array_values($data);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    
    return false;
}

/**
 * SELECT - Read records from database
 * 
 * @param string $table - Table name
 * @param array $conditions - WHERE conditions (column => value)
 * @param string $columns - Columns to select (default: *)
 * @return array|false - Array of results or false on failure
 */
function db_select($table, $conditions = [], $columns = '*') {
    global $conn;
    
    $sql = "SELECT $columns FROM $table";
    
    // Add WHERE clause if conditions exist
    if (!empty($conditions)) {
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = ?";
        }
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Bind parameters if conditions exist
    if (!empty($conditions)) {
        $types = str_repeat('s', count($conditions));
        $values = array_values($conditions);
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all results as associative array
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
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
    
    // Prepare SET clause
    $set = [];
    foreach ($data as $column => $value) {
        $set[] = "$column = ?";
    }
    
    // Prepare WHERE clause
    $where = [];
    foreach ($conditions as $column => $value) {
        $where[] = "$column = ?";
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Combine data and conditions for binding
    $values = array_merge(array_values($data), array_values($conditions));
    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);
    
    return $stmt->execute();
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
    
    // Prepare WHERE clause
    $where = [];
    foreach ($conditions as $column => $value) {
        $where[] = "$column = ?";
    }
    
    $sql = "DELETE FROM $table WHERE " . implode(' AND ', $where);
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    $types = str_repeat('s', count($conditions));
    $values = array_values($conditions);
    $stmt->bind_param($types, ...$values);
    
    return $stmt->execute();
}

/**
 * QUERY - Execute custom SQL query
 * 
 * @param string $sql - SQL query
 * @param array $params - Parameters for prepared statement
 * @return array|bool - Results for SELECT, true/false for others
 */
function db_query($sql, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Bind parameters if provided
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    
    // Check if query returns results (SELECT)
    if ($result = $stmt->get_result()) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    // For INSERT, UPDATE, DELETE
    return $stmt->affected_rows >= 0;
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
    
    $sql = "SELECT COUNT(*) as count FROM $table";
    
    if (!empty($conditions)) {
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = ?";
        }
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return 0;
    }
    
    if (!empty($conditions)) {
        $types = str_repeat('s', count($conditions));
        $values = array_values($conditions);
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return (int)$row['count'];
}

/**
 * Close database connection
 */
function db_close() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// Helper function to escape output (XSS prevention)
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

?>
