<?php
/**
 * Session Testing Page
 * Comprehensive testing for Session model methods
 */

session_start();
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/models/User.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Model Testing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        .test-section {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4CAF50;
            margin-top: 0;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .info {
            color: #2196F3;
        }
        pre {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #4CAF50;
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-upcoming { background: #2196F3; color: white; }
        .badge-completed { background: #4CAF50; color: white; }
        .badge-canceled { background: #f44336; color: white; }
        .badge-online { background: #2196F3; color: white; }
        .badge-in-person { background: #9C27B0; color: white; }
        .count {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <h1>🧪 Session Model Testing Page</h1>
    
    <!-- Test 1: Database Connection -->
    <div class="test-section">
        <h2>1. Database Connection Test</h2>
        <?php
        global $conn;
        if ($conn) {
            echo "<p class='success'>✓ Database connection successful</p>";
            echo "<p class='info'>Database: " . $conn->query('SELECT DATABASE()')->fetchColumn() . "</p>";
        } else {
            echo "<p class='error'>✗ Database connection failed</p>";
        }
        ?>
    </div>

    <!-- Test 2: Get All Sessions -->
    <div class="test-section">
        <h2>2. Get All Sessions (No Filters - ALL Statuses)</h2>
        <?php
        try {
            // Pass empty string for status to bypass the 'upcoming' default filter
            $all_sessions = Session::getAllSessions(['status' => ''], 10);
            if ($all_sessions && count($all_sessions) > 0) {
                echo "<p class='success'>✓ Found " . count($all_sessions) . " sessions</p>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Title</th><th>Instructor</th><th>Category</th><th>Status</th><th>Type</th><th>Date/Time</th></tr>";
                foreach ($all_sessions as $session) {
                    $status_class = 'badge-' . strtolower($session['status']);
                    $type_class = $session['location_type'] == 'online' ? 'badge-online' : 'badge-in-person';
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($session['session_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($session['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($session['instructor_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($session['category_name']) . "</td>";
                    echo "<td><span class='badge $status_class'>" . htmlspecialchars($session['status']) . "</span></td>";
                    echo "<td><span class='badge $type_class'>" . htmlspecialchars($session['location_type']) . "</span></td>";
                    echo "<td>" . htmlspecialchars($session['event_datetime']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>✗ No sessions found via Session::getAllSessions()</p>";
                
                // Debug: Check directly in database
                global $conn;
                $debug_stmt = $conn->query("SELECT COUNT(*) as total FROM skill_sessions");
                $debug_count = $debug_stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p class='info'>📊 Raw database count: " . $debug_count['total'] . " sessions in skill_sessions table</p>";
                
                if ($debug_count['total'] > 0) {
                    $debug_stmt2 = $conn->query("SELECT status, COUNT(*) as count FROM skill_sessions GROUP BY status");
                    $status_breakdown = $debug_stmt2->fetchAll(PDO::FETCH_ASSOC);
                    echo "<p class='info'>Status breakdown:</p><ul>";
                    foreach ($status_breakdown as $row) {
                        echo "<li>" . htmlspecialchars($row['status']) . ": " . $row['count'] . "</li>";
                    }
                    echo "</ul>";
                    
                    // Check for JOIN issues
                    echo "<h3 style='color: #FF9800;'>🔍 Diagnosing JOIN Issues:</h3>";
                    
                    // Check sessions without valid instructor
                    $orphan_instructor = $conn->query("
                        SELECT COUNT(*) as count 
                        FROM skill_sessions s 
                        LEFT JOIN Users u ON s.instructor_id = u.user_id 
                        WHERE u.user_id IS NULL
                    ")->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='info'>Sessions with missing instructor: <strong>" . $orphan_instructor['count'] . "</strong></p>";
                    
                    // Check sessions without valid category
                    $orphan_category = $conn->query("
                        SELECT COUNT(*) as count 
                        FROM skill_sessions s 
                        LEFT JOIN Categories c ON s.category_id = c.category_id 
                        WHERE c.category_id IS NULL
                    ")->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='info'>Sessions with missing category: <strong>" . $orphan_category['count'] . "</strong></p>";
                    
                    // Try a LEFT JOIN query to see what we get
                    $left_join_test = $conn->query("
                        SELECT s.*, u.full_name as instructor_name, c.name as category_name
                        FROM skill_sessions s
                        LEFT JOIN Users u ON s.instructor_id = u.user_id
                        LEFT JOIN Categories c ON s.category_id = c.category_id
                        LIMIT 3
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<p class='info'>Sample sessions with LEFT JOIN:</p>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Title</th><th>Instructor ID</th><th>Instructor Name</th><th>Category ID</th><th>Category Name</th></tr>";
                    foreach ($left_join_test as $s) {
                        echo "<tr>";
                        echo "<td>" . $s['session_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($s['title']) . "</td>";
                        echo "<td>" . $s['instructor_id'] . "</td>";
                        echo "<td>" . ($s['instructor_name'] ? htmlspecialchars($s['instructor_name']) : '<span style="color:red;">NULL</span>') . "</td>";
                        echo "<td>" . $s['category_id'] . "</td>";
                        echo "<td>" . ($s['category_name'] ? htmlspecialchars($s['category_name']) : '<span style="color:red;">NULL</span>') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 3: Get Sessions by Status -->
    <div class="test-section">
        <h2>3. Get Sessions by Status (Upcoming, Completed, Canceled)</h2>
        <?php
        try {
            $upcoming_sessions = Session::getAllSessions(['status' => 'upcoming'], 5);
            $completed_sessions = Session::getAllSessions(['status' => 'completed'], 5);
            $canceled_sessions = Session::getAllSessions(['status' => 'canceled'], 5);
            
            echo "<p><strong>Upcoming:</strong> <span class='count'>" . (is_array($upcoming_sessions) ? count($upcoming_sessions) : 0) . "</span></p>";
            if (is_array($upcoming_sessions) && count($upcoming_sessions) > 0) {
                echo "<ul>";
                foreach (array_slice($upcoming_sessions, 0, 3) as $session) {
                    echo "<li>" . htmlspecialchars($session['title']) . " - " . htmlspecialchars($session['instructor_name']) . "</li>";
                }
                echo "</ul>";
            }
            
            echo "<p><strong>Completed:</strong> <span class='count'>" . (is_array($completed_sessions) ? count($completed_sessions) : 0) . "</span></p>";
            if (is_array($completed_sessions) && count($completed_sessions) > 0) {
                echo "<ul>";
                foreach (array_slice($completed_sessions, 0, 3) as $session) {
                    echo "<li>" . htmlspecialchars($session['title']) . " - " . htmlspecialchars($session['instructor_name']) . "</li>";
                }
                echo "</ul>";
            }
            
            echo "<p><strong>Canceled:</strong> <span class='count'>" . (is_array($canceled_sessions) ? count($canceled_sessions) : 0) . "</span></p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 4: Get Sessions by Category -->
    <div class="test-section">
        <h2>4. Get Sessions by Category</h2>
        <?php
        try {
            // Get all categories using direct query for ORDER BY support
            global $conn;
            $stmt = $conn->query("SELECT * FROM Categories ORDER BY name ASC");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($categories && count($categories) > 0) {
                echo "<p class='success'>✓ Found " . count($categories) . " categories</p>";
                echo "<table>";
                echo "<tr><th>Category</th><th>Session Count</th></tr>";
                foreach ($categories as $category) {
                    // Use empty status to get all sessions regardless of status
                    $category_sessions = Session::getAllSessions([
                        'category_id' => $category['category_id'],
                        'status' => ''
                    ]);
                    $count = (is_array($category_sessions)) ? count($category_sessions) : 0;
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($category['name']) . "</strong></td>";
                    echo "<td><span class='count'>$count</span></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='info'>No categories found</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 5: Get Sessions by Location Type -->
    <div class="test-section">
        <h2>5. Get Sessions by Location Type</h2>
        <?php
        try {
            // Use empty status to get all sessions regardless of status
            $online_sessions = Session::getAllSessions(['location_type' => 'online', 'status' => '']);
            $in_person_sessions = Session::getAllSessions(['location_type' => 'in-person', 'status' => '']);
            
            $online_count = (is_array($online_sessions)) ? count($online_sessions) : 0;
            $in_person_count = (is_array($in_person_sessions)) ? count($in_person_sessions) : 0;
            
            echo "<p><strong>Online Sessions:</strong> <span class='count'>$online_count</span></p>";
            echo "<p><strong>In-Person Sessions:</strong> <span class='count'>$in_person_count</span></p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 6: Search Sessions -->
    <div class="test-section">
        <h2>6. Search Sessions</h2>
        <?php
        try {
            // Try multiple search terms
            $search_terms = ['session', 'skill', 'workshop', 'learn', 'class'];
            $found_results = false;
            
            foreach ($search_terms as $term) {
                $search_results = Session::getAllSessions(['search' => $term, 'status' => ''], 3);
                if (is_array($search_results) && count($search_results) > 0) {
                    echo "<p class='success'>✓ Found " . count($search_results) . " sessions matching '<strong>$term</strong>'</p>";
                    echo "<ul>";
                    foreach ($search_results as $session) {
                        echo "<li><strong>" . htmlspecialchars($session['title']) . "</strong><br>";
                        echo "<small>" . substr(htmlspecialchars($session['description']), 0, 100) . "...</small></li>";
                    }
                    echo "</ul>";
                    $found_results = true;
                    break; // Stop after first successful search
                }
            }
            
            if (!$found_results) {
                echo "<p class='info'>No sessions found with common search terms</p>";
                
                // Show sample titles from database
                global $conn;
                $sample_stmt = $conn->query("SELECT title, description FROM skill_sessions LIMIT 5");
                $samples = $sample_stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($samples && count($samples) > 0) {
                    echo "<p class='info'>📋 Sample session titles in database:</p>";
                    echo "<ul>";
                    foreach ($samples as $s) {
                        echo "<li>" . htmlspecialchars($s['title']) . "</li>";
                    }
                    echo "</ul>";
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 7: Get Session by ID -->
    <div class="test-section">
        <h2>7. Get Session by ID (First Available Session)</h2>
        <?php
        try {
            $first_session = Session::getAllSessions(['status' => ''], 1);
            if (is_array($first_session) && count($first_session) > 0) {
                $session_id = $first_session[0]['session_id'];
                echo "<p class='info'>Testing with session ID: <strong>$session_id</strong></p>";
                
                $session_detail = Session::getSessionById($session_id);
                
                if ($session_detail) {
                    echo "<p class='success'>✓ Successfully retrieved session #$session_id</p>";
                    
                    // Display key details in a table
                    echo "<table>";
                    echo "<tr><th>Field</th><th>Value</th></tr>";
                    echo "<tr><td>Title</td><td>" . htmlspecialchars($session_detail['title']) . "</td></tr>";
                    echo "<tr><td>Instructor</td><td>" . htmlspecialchars($session_detail['instructor_name']) . "</td></tr>";
                    echo "<tr><td>Category</td><td>" . htmlspecialchars($session_detail['category_name']) . "</td></tr>";
                    echo "<tr><td>Status</td><td><span class='badge badge-" . strtolower($session_detail['status']) . "'>" . htmlspecialchars($session_detail['status']) . "</span></td></tr>";
                    echo "<tr><td>Location Type</td><td>" . htmlspecialchars($session_detail['location_type']) . "</td></tr>";
                    echo "<tr><td>Date/Time</td><td>" . htmlspecialchars($session_detail['event_datetime']) . "</td></tr>";
                    echo "<tr><td>Duration</td><td>" . htmlspecialchars($session_detail['duration_minutes']) . " minutes</td></tr>";
                    echo "<tr><td>Fee</td><td>" . htmlspecialchars($session_detail['fee_type']) . " (" . ($session_detail['fee_amount'] > 0 ? '$' . $session_detail['fee_amount'] : 'Free') . ")</td></tr>";
                    echo "<tr><td>Capacity</td><td>" . htmlspecialchars($session_detail['capacity_remaining']) . " / " . htmlspecialchars($session_detail['total_capacity']) . "</td></tr>";
                    echo "<tr><td>Average Rating</td><td>" . number_format($session_detail['average_rating'], 1) . " ⭐</td></tr>";
                    echo "</table>";
                } else {
                    echo "<p class='error'>✗ Could not retrieve session details for ID: $session_id</p>";
                    
                    // Debug: Check what's actually in the session record
                    global $conn;
                    $debug_stmt = $conn->prepare("SELECT * FROM skill_sessions WHERE session_id = ?");
                    $debug_stmt->execute([$session_id]);
                    $session_data = $debug_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($session_data) {
                        echo "<p class='info'>📋 Session exists in database but getSessionById() returned false</p>";
                        echo "<p class='info'>This might be due to missing instructor or category records</p>";
                        
                        // Check instructor
                        $instructor_check = $conn->prepare("SELECT user_id, full_name FROM Users WHERE user_id = ?");
                        $instructor_check->execute([$session_data['instructor_id']]);
                        $instructor = $instructor_check->fetch(PDO::FETCH_ASSOC);
                        
                        if ($instructor) {
                            echo "<p class='success'>✓ Instructor exists: " . htmlspecialchars($instructor['full_name']) . " (ID: {$session_data['instructor_id']})</p>";
                        } else {
                            echo "<p class='error'>✗ Instructor ID {$session_data['instructor_id']} not found in Users table</p>";
                        }
                        
                        // Check category
                        $category_check = $conn->prepare("SELECT category_id, name FROM Categories WHERE category_id = ?");
                        $category_check->execute([$session_data['category_id']]);
                        $category = $category_check->fetch(PDO::FETCH_ASSOC);
                        
                        if ($category) {
                            echo "<p class='success'>✓ Category exists: " . htmlspecialchars($category['name']) . " (ID: {$session_data['category_id']})</p>";
                        } else {
                            echo "<p class='error'>✗ Category ID {$session_data['category_id']} not found in Categories table</p>";
                        }
                        
                        // Test the exact query used by getSessionById()
                        echo "<h4>Testing getSessionById() Query:</h4>";
                        try {
                            $test_sql = "SELECT s.*, 
                                   u.full_name as instructor_name,
                                   u.email as instructor_email,
                                   u.city as instructor_city,
                                   c.name as category_name,
                                   COALESCE(AVG(r.rating), 0) as average_rating,
                                   COUNT(r.rating_id) as total_ratings
                            FROM skill_sessions s
                            INNER JOIN Users u ON s.instructor_id = u.user_id
                            INNER JOIN Categories c ON s.category_id = c.category_id
                            LEFT JOIN ratings r ON s.session_id = r.session_id
                            WHERE s.session_id = ?
                            GROUP BY s.session_id";
                            
                            $test_stmt = $conn->prepare($test_sql);
                            $test_stmt->execute([$session_id]);
                            $test_result = $test_stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($test_result) {
                                echo "<p class='success'>✓ Direct query successful! Found session data.</p>";
                                echo "<p class='info'>The query works, so the issue might be in Session.php error handling.</p>";
                                
                                echo "<h4>Session Details from Direct Query:</h4>";
                                echo "<table>";
                                echo "<tr><th>Field</th><th>Value</th></tr>";
                                echo "<tr><td>Title</td><td>" . htmlspecialchars($test_result['title']) . "</td></tr>";
                                echo "<tr><td>Instructor</td><td>" . htmlspecialchars($test_result['instructor_name']) . "</td></tr>";
                                echo "<tr><td>Category</td><td>" . htmlspecialchars($test_result['category_name']) . "</td></tr>";
                                echo "<tr><td>Status</td><td>" . htmlspecialchars($test_result['status']) . "</td></tr>";
                                echo "</table>";
                            } else {
                                echo "<p class='error'>✗ Direct query returned no results</p>";
                                
                                // Check PDO error
                                $error_info = $test_stmt->errorInfo();
                                if ($error_info[0] !== '00000') {
                                    echo "<p class='error'>SQL Error: " . htmlspecialchars($error_info[2]) . "</p>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo "<p class='error'>✗ Query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
                        }
                        
                        // Show the raw session data
                        echo "<h4>Raw Session Data:</h4>";
                        echo "<table>";
                        echo "<tr><th>Field</th><th>Value</th></tr>";
                        foreach ($session_data as $key => $value) {
                            echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p class='info'>📋 Session ID $session_id not found in database</p>";
                    }
                }
            } else {
                echo "<p class='info'>No sessions available to test</p>";
                
                // Check if any sessions exist at all
                global $conn;
                $count_stmt = $conn->query("SELECT COUNT(*) as total FROM skill_sessions");
                $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p class='info'>📊 Total sessions in database: " . $count_result['total'] . "</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 8: Get Sessions by Instructor -->
    <div class="test-section">
        <h2>8. Get Sessions by Instructor (First Instructor)</h2>
        <?php
        try {
            // Get first instructor user
            $instructors = db_select('Users', ['role' => 'instructor'], 1);
            if ($instructors && count($instructors) > 0) {
                $instructor_id = $instructors[0]['user_id'];
                $instructor_name = $instructors[0]['full_name'];
                
                $instructor_sessions = Session::getSessionsByInstructor($instructor_id);
                
                echo "<p class='success'>✓ Instructor: " . htmlspecialchars($instructor_name) . "</p>";
                echo "<p><strong>Total Sessions:</strong> <span class='count'>" . (count($instructor_sessions) ?: 0) . "</span></p>";
                
                if ($instructor_sessions && count($instructor_sessions) > 0) {
                    echo "<ul>";
                    foreach ($instructor_sessions as $session) {
                        echo "<li>" . htmlspecialchars($session['title']) . " - <span class='badge badge-" . strtolower($session['status']) . "'>" . htmlspecialchars($session['status']) . "</span></li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "<p class='info'>No instructors found in database</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 9: Session Statistics -->
    <div class="test-section">
        <h2>9. Session Statistics</h2>
        <?php
        try {
            global $conn;
            
            // Count by status
            $stmt = $conn->query("
                SELECT status, COUNT(*) as count 
                FROM skill_sessions 
                GROUP BY status
            ");
            $status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Sessions by Status:</h3>";
            echo "<table>";
            echo "<tr><th>Status</th><th>Count</th></tr>";
            foreach ($status_counts as $row) {
                echo "<tr><td><span class='badge badge-" . strtolower($row['status']) . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                echo "<td><span class='count'>" . $row['count'] . "</span></td></tr>";
            }
            echo "</table>";
            
            // Count by fee type
            $stmt = $conn->query("
                SELECT 
                    fee_type,
                    COUNT(*) as count 
                FROM skill_sessions 
                GROUP BY fee_type
            ");
            $fee_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Sessions by Fee Type:</h3>";
            echo "<table>";
            echo "<tr><th>Type</th><th>Count</th></tr>";
            foreach ($fee_counts as $row) {
                echo "<tr><td>" . htmlspecialchars($row['fee_type']) . "</td>";
                echo "<td><span class='count'>" . $row['count'] . "</span></td></tr>";
            }
            echo "</table>";
            
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 10: Upcoming Sessions -->
    <div class="test-section">
        <h2>10. Upcoming Sessions (Future Dates)</h2>
        <?php
        try {
            global $conn;
            $stmt = $conn->prepare("
                SELECT s.*, u.full_name as instructor_name, c.name as category_name
                FROM skill_sessions s
                JOIN Users u ON s.instructor_id = u.user_id
                JOIN Categories c ON s.category_id = c.category_id
                WHERE s.event_datetime > NOW() 
                AND s.status = 'upcoming'
                ORDER BY s.event_datetime ASC
                LIMIT 5
            ");
            $stmt->execute();
            $upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($upcoming && count($upcoming) > 0) {
                echo "<p class='success'>✓ Found " . count($upcoming) . " upcoming sessions</p>";
                echo "<table>";
                echo "<tr><th>Title</th><th>Instructor</th><th>Date/Time</th><th>Category</th></tr>";
                foreach ($upcoming as $session) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($session['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($session['instructor_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($session['event_datetime']) . "</td>";
                    echo "<td>" . htmlspecialchars($session['category_name']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='info'>No upcoming sessions found</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div style="text-align: center; margin-top: 30px; padding: 20px; background: #e8f5e9; border-radius: 8px;">
        <p><strong>Testing Complete!</strong></p>
        <p><a href="index.php">← Back to Home</a> | <a href="sessions.php">View Sessions</a></p>
    </div>

</body>
</html>
