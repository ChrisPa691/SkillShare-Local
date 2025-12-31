<?php
/**
 * Database Test Script
 * Run this file to verify database connection and data
 */

require_once 'app/config/database.php';

echo "<h1>Database Connection Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
    table { border-collapse: collapse; width: 100%; background: white; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #667eea; color: white; }
</style>";

// Test 1: Connection
echo "<div class='info'>";
echo "<h2>1. Connection Test</h2>";
if ($conn) {
    // Check connection type and test accordingly
    if ($conn instanceof mysqli) {
        if ($conn->ping()) {
            echo "<p class='success'>✓ Database connection successful! (mysqli)</p>";
        } else {
            echo "<p class='error'>✗ Database connection failed!</p>";
            die();
        }
    } elseif ($conn instanceof PDO) {
        try {
            $conn->query('SELECT 1');
            echo "<p class='success'>✓ Database connection successful! (PDO)</p>";
        } catch (PDOException $e) {
            echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
            die();
        }
    } else {
        echo "<p class='error'>✗ Unknown connection type!</p>";
        die();
    }
} else {
    echo "<p class='error'>✗ Database connection object not found!</p>";
    die();
}
echo "</div>";

// Test 2: Tables exist
echo "<div class='info'>";
echo "<h2>2. Tables Test</h2>";
$tables = ['Users', 'Categories', 'skill_sessions', 'bookings', 'ratings', 'impact_factors', 'admin_actions'];
foreach ($tables as $table) {
    if ($conn instanceof mysqli) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✓ Table '$table' exists</p>";
        } else {
            echo "<p class='error'>✗ Table '$table' missing</p>";
        }
    } elseif ($conn instanceof PDO) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt && $stmt->rowCount() > 0) {
            echo "<p class='success'>✓ Table '$table' exists</p>";
        } else {
            echo "<p class='error'>✗ Table '$table' missing</p>";
        }
    }
}
echo "</div>";

// Test 3: Count records
echo "<div class='info'>";
echo "<h2>3. Data Count Test</h2>";
echo "<table>";
echo "<tr><th>Table</th><th>Record Count</th></tr>";
foreach ($tables as $table) {
    $count = db_count($table);
    echo "<tr><td>$table</td><td><strong>$count</strong> records</td></tr>";
}
echo "</table>";
echo "</div>";

// Test 4: Sample Users
echo "<div class='info'>";
echo "<h2>4. Sample Users</h2>";
$users = db_select('Users', [], 5); // Get first 5 users
if ($users && count($users) > 0) {
    // Debug: Check the structure of the first user
    echo "<details><summary>Debug: Array Structure</summary>";
    echo "<pre>" . print_r($users[0], true) . "</pre>";
    echo "</details>";
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>City</th></tr>";
    foreach ($users as $user) {
        // Handle both numeric and associative arrays
        $user_id = $user['user_id'] ?? $user[0] ?? 'N/A';
        $full_name = $user['full_name'] ?? $user[1] ?? 'N/A';
        $email = $user['email'] ?? $user[2] ?? 'N/A';
        $role = $user['role'] ?? $user[4] ?? 'N/A';
        $city = $user['city'] ?? $user[5] ?? 'N/A';
        
        echo "<tr>";
        echo "<td>{$user_id}</td>";
        echo "<td>{$full_name}</td>";
        echo "<td>{$email}</td>";
        echo "<td>{$role}</td>";
        echo "<td>{$city}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No users found</p>";
}
echo "</div>";

// Test 5: Test Login Credentials
echo "<div class='info'>";
echo "<h2>5. Test User Credentials</h2>";
$testUsers = [
    ['email' => 'learner@test.com', 'password' => 'Learner123', 'role' => 'learner'],
    ['email' => 'instructor@test.com', 'password' => 'Instructor123', 'role' => 'instructor'],
    ['email' => 'admin@test.com', 'password' => 'Admin123', 'role' => 'admin']
];

foreach ($testUsers as $testUser) {
    $user = db_select_one('Users', ['email' => $testUser['email']]);
    if ($user && password_verify($testUser['password'], $user['password_hash'])) {
        echo "<p class='success'>✓ {$testUser['role']} login works: {$testUser['email']} / {$testUser['password']}</p>";
    } else {
        echo "<p class='error'>✗ {$testUser['role']} login failed</p>";
    }
}
echo "</div>";

// Test 6: Sample Sessions
echo "<div class='info'>";
echo "<h2>6. Sample Sessions</h2>";
$sessions = db_select('skill_sessions', [], 5);
if ($sessions && count($sessions) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Capacity</th><th>Event Date</th></tr>";
    foreach ($sessions as $session) {
        // Handle both numeric and associative arrays
        $session_id = $session['session_id'] ?? $session[0] ?? 'N/A';
        $title = $session['title'] ?? $session[3] ?? 'N/A';
        $status = $session['status'] ?? $session[13] ?? 'N/A';
        $capacity_remaining = $session['capacity_remaining'] ?? 'N/A';
        $total_capacity = $session['total_capacity'] ?? 'N/A';
        $event_datetime = $session['event_datetime'] ?? $session[11] ?? 'N/A';
        
        echo "<tr>";
        echo "<td>{$session_id}</td>";
        echo "<td>{$title}</td>";
        echo "<td>{$status}</td>";
        echo "<td>{$capacity_remaining}/{$total_capacity}</td>";
        echo "<td>{$event_datetime}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No sessions found</p>";
}
echo "</div>";

// Test 7: Categories
echo "<div class='info'>";
echo "<h2>7. Categories</h2>";
$categories = db_select('Categories');
if ($categories && count($categories) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th></tr>";
    foreach ($categories as $cat) {
        // Handle both numeric and associative arrays
        $category_id = $cat['category_id'] ?? $cat[0] ?? 'N/A';
        $name = $cat['name'] ?? $cat[1] ?? 'N/A';
        $description = $cat['description'] ?? $cat[2] ?? 'N/A';
        
        echo "<tr>";
        echo "<td>{$category_id}</td>";
        echo "<td>{$name}</td>";
        echo "<td>{$description}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No categories found</p>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>✓ All Tests Complete!</h2>";
echo "<p><a href='public/login.php'>Go to Login Page</a></p>";
echo "</div>";
?>
