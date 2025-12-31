<?php
session_start();
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Session.php';

echo "<h2>Database Connection Test</h2>";

// Test 1: Check if categories exist
echo "<h3>1. Categories in Database:</h3>";
$categories = db_select('Categories', [], 'ORDER BY name ASC');
echo "<pre>";
print_r($categories);
echo "</pre>";

// Test 2: Check if sessions exist
echo "<h3>2. Sessions in Database (Raw Query):</h3>";
global $conn;
try {
    $stmt = $conn->query("SELECT session_id, title, status, event_datetime FROM skill_sessions LIMIT 5");
    $sessions_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($sessions_raw);
    echo "</pre>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Test 3: Check getAllSessions without filters
echo "<h3>3. Session::getAllSessions() with no filters:</h3>";
try {
    $all_sessions = Session::getAllSessions([], 5);
    echo "<pre>";
    print_r($all_sessions);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test 4: Check with status filter removed
echo "<h3>4. Session::getAllSessions() with status='':</h3>";
try {
    $all_sessions2 = Session::getAllSessions(['status' => ''], 5);
    echo "<pre>";
    print_r($all_sessions2);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test 5: Count total sessions by status
echo "<h3>5. Sessions Count by Status:</h3>";
try {
    $stmt = $conn->query("SELECT status, COUNT(*) as count FROM skill_sessions GROUP BY status");
    $status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($status_counts);
    echo "</pre>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
