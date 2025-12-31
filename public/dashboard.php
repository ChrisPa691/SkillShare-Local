<?php
/**
 * Unified Dashboard
 * Role-based content display for all authenticated users
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';

// Require authentication
require_login();

// Get user details
$user_id = get_user_id();
$user_name = get_user_name();
$user_email = get_user_email();
$role = get_user_role();
$city = get_user_city();

// Set page title based on role
$page_title = ucfirst($role) . " Dashboard";

// Include header and navbar
include '../app/includes/header.php';
include '../app/includes/navbar.php';
?>

<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .dashboard-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .dashboard-card h3 {
        color: #667eea;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dashboard-card h3 i {
        font-size: 1.5rem;
    }
    
    .stat-box {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-left: 4px solid #667eea;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin: 0;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 1rem;
        margin: 0;
    }
    
    .action-btn {
        display: inline-block;
        padding: 12px 30px;
        background: linear-gradient(135deg, #28a745 0%, #17a2b8 100%);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        color: white;
    }
    
    .action-btn i {
        margin-right: 8px;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .badge-role {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .badge-learner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .badge-instructor {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .badge-admin {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    
    .quick-action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .quick-action-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: #495057;
        transition: all 0.3s ease;
    }
    
    .quick-action-card:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-3px);
    }
    
    .quick-action-card i {
        font-size: 2.5rem;
        margin-bottom: 10px;
        display: block;
    }
</style>

<div class="container mt-4">
    <!-- Flash Messages -->
    <?php display_flash(); ?>
    
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-tachometer-alt"></i>
                        Welcome back, <?php echo escape($user_name); ?>!
                    </h1>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i><?php echo escape($user_email); ?>
                        <span class="mx-3">|</span>
                        <i class="fas fa-map-marker-alt me-2"></i><?php echo escape($city); ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge-role badge-<?php echo $role; ?>">
                        <i class="fas fa-user-circle"></i>
                        <?php echo ucfirst($role); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?php if ($role === 'learner'): ?>
        <!-- LEARNER DASHBOARD -->
        <div class="row">
            <!-- Quick Stats -->
            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-chart-line"></i> Your Progress</h3>
                    <div class="stat-box">
                        <p class="stat-number">0</p>
                        <p class="stat-label">Sessions Booked</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">0</p>
                        <p class="stat-label">Sessions Completed</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">0 hrs</p>
                        <p class="stat-label">Learning Time</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="col-md-8 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-calendar-check"></i> Upcoming Booked Sessions</h3>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>You haven't booked any sessions yet.</p>
                        <a href="sessions.php" class="action-btn">
                            <i class="fas fa-search"></i> Browse Sessions
                        </a>
                    </div>
                </div>
            </div>

            <!-- My Bookings -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-bookmark"></i> My Bookings</h3>
                    <div class="empty-state">
                        <p class="text-muted">No bookings to display</p>
                    </div>
                </div>
            </div>

            <!-- Sessions to Rate -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-star"></i> Rate Completed Sessions</h3>
                    <div class="empty-state">
                        <p class="text-muted">No sessions to rate yet</p>
                        <small>Complete sessions to leave ratings</small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-12 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="quick-action-grid">
                        <a href="sessions.php" class="quick-action-card">
                            <i class="fas fa-search"></i>
                            <strong>Browse Sessions</strong>
                        </a>
                        <a href="sessions.php?filter=upcoming" class="quick-action-card">
                            <i class="fas fa-calendar-alt"></i>
                            <strong>Upcoming Sessions</strong>
                        </a>
                        <a href="impact_dashboard.php" class="quick-action-card">
                            <i class="fas fa-leaf"></i>
                            <strong>Impact Dashboard</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($role === 'instructor'): ?>
        <!-- INSTRUCTOR DASHBOARD -->
        <div class="row">
            <!-- Quick Stats -->
            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-chart-bar"></i> Your Impact</h3>
                    <div class="stat-box">
                        <p class="stat-number">0</p>
                        <p class="stat-label">Active Sessions</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">0</p>
                        <p class="stat-label">Total Learners</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">0.0</p>
                        <p class="stat-label">Average Rating</p>
                    </div>
                </div>
            </div>

            <!-- My Sessions -->
            <div class="col-md-8 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-chalkboard-teacher"></i> My Sessions</h3>
                    <div class="empty-state">
                        <i class="fas fa-chalkboard"></i>
                        <p>You haven't created any sessions yet.</p>
                        <a href="session_create.php" class="action-btn">
                            <i class="fas fa-plus-circle"></i> Create Your First Session
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Booking Requests -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-bell"></i> Pending Booking Requests</h3>
                    <div class="empty-state">
                        <p class="text-muted">No pending requests</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-calendar"></i> Upcoming Sessions</h3>
                    <div class="empty-state">
                        <p class="text-muted">No upcoming sessions</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-12 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="quick-action-grid">
                        <a href="session_create.php" class="quick-action-card">
                            <i class="fas fa-plus-circle"></i>
                            <strong>Create Session</strong>
                        </a>
                        <a href="sessions.php?instructor=<?php echo $user_id; ?>" class="quick-action-card">
                            <i class="fas fa-list"></i>
                            <strong>My Sessions</strong>
                        </a>
                        <a href="bookings.php" class="quick-action-card">
                            <i class="fas fa-users"></i>
                            <strong>Manage Bookings</strong>
                        </a>
                        <a href="impact_dashboard.php" class="quick-action-card">
                            <i class="fas fa-leaf"></i>
                            <strong>View Impact</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($role === 'admin'): ?>
        <!-- ADMIN DASHBOARD -->
        <div class="row">
            <!-- Platform Statistics -->
            <div class="col-md-3 mb-4">
                <div class="dashboard-card text-center">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <p class="stat-number">
                        <?php echo db_count('Users'); ?>
                    </p>
                    <p class="stat-label">Total Users</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="dashboard-card text-center">
                    <i class="fas fa-chalkboard fa-3x text-success mb-3"></i>
                    <p class="stat-number">
                        <?php echo db_count('skill_sessions'); ?>
                    </p>
                    <p class="stat-label">Total Sessions</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="dashboard-card text-center">
                    <i class="fas fa-bookmark fa-3x text-info mb-3"></i>
                    <p class="stat-number">
                        <?php echo db_count('bookings'); ?>
                    </p>
                    <p class="stat-label">Total Bookings</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="dashboard-card text-center">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <p class="stat-number">
                        <?php echo db_count('ratings'); ?>
                    </p>
                    <p class="stat-label">Total Ratings</p>
                </div>
            </div>

            <!-- User Breakdown -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-users-cog"></i> User Breakdown</h3>
                    <div class="stat-box">
                        <p class="stat-number">
                            <?php echo db_count('Users', ['role' => 'learner']); ?>
                        </p>
                        <p class="stat-label">Learners</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">
                            <?php echo db_count('Users', ['role' => 'instructor']); ?>
                        </p>
                        <p class="stat-label">Instructors</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">
                            <?php echo db_count('Users', ['role' => 'admin']); ?>
                        </p>
                        <p class="stat-label">Admins</p>
                    </div>
                </div>
            </div>

            <!-- Session Status -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-chart-pie"></i> Session Status</h3>
                    <div class="stat-box">
                        <p class="stat-number">
                            <?php echo db_count('skill_sessions', ['session_status' => 'active']); ?>
                        </p>
                        <p class="stat-label">Active Sessions</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">
                            <?php echo db_count('skill_sessions', ['session_status' => 'completed']); ?>
                        </p>
                        <p class="stat-label">Completed Sessions</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number">
                            <?php echo db_count('skill_sessions', ['session_status' => 'cancelled']); ?>
                        </p>
                        <p class="stat-label">Cancelled Sessions</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-md-12 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-history"></i> Recent Activity</h3>
                    <div class="empty-state">
                        <p class="text-muted">Activity feed coming soon</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-12 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-bolt"></i> Admin Actions</h3>
                    <div class="quick-action-grid">
                        <a href="admin_users.php" class="quick-action-card">
                            <i class="fas fa-users"></i>
                            <strong>Manage Users</strong>
                        </a>
                        <a href="admin_sessions.php" class="quick-action-card">
                            <i class="fas fa-chalkboard"></i>
                            <strong>Manage Sessions</strong>
                        </a>
                        <a href="admin_bookings.php" class="quick-action-card">
                            <i class="fas fa-bookmark"></i>
                            <strong>View Bookings</strong>
                        </a>
                        <a href="impact_dashboard.php" class="quick-action-card">
                            <i class="fas fa-leaf"></i>
                            <strong>Impact Dashboard</strong>
                        </a>
                        <a href="admin_settings.php" class="quick-action-card">
                            <i class="fas fa-cog"></i>
                            <strong>Settings</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php include '../app/includes/footer.php'; ?>
