<?php
/**
 * Unified Dashboard
 * Role-based content display for all authenticated users
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';
require_once '../app/models/Booking.php';
require_once '../app/models/Session.php';
require_once '../app/models/Rating.php';

// Require authentication
require_login();

// Get user details
$user_id = get_user_id();
$user_name = get_user_name();
$user_email = get_user_email();
$role = get_user_role();
$city = get_user_city();

// Fetch role-specific data
if ($role === 'learner') {
    // Get learner's bookings
    $my_bookings = Booking::getBookingsByLearner($user_id);
    
    // Get sessions to rate (accepted bookings for completed sessions without ratings)
    $sessions_to_rate = [];
    if ($my_bookings) {
        foreach ($my_bookings as $booking) {
            if ($booking['status'] === 'accepted' && $booking['session_status'] === 'completed') {
                // Check if rating exists
                $existing_rating = Rating::getRatingByLearnerAndSession($user_id, $booking['session_id']);
                if (!$existing_rating) {
                    $sessions_to_rate[] = $booking;
                }
            }
        }
    }
} elseif ($role === 'instructor') {
    // Get instructor's sessions
    $my_sessions = Session::getSessionsByInstructor($user_id);
    
    // Get instructor stats
    $instructor_stats = Session::getInstructorStats($user_id);
    
    // Get pending booking requests across all instructor's sessions
    $pending_requests = [];
    if ($my_sessions) {
        foreach ($my_sessions as $session) {
            $session_bookings = Booking::getBookingsBySession($session['session_id'], 'pending');
            if ($session_bookings) {
                foreach ($session_bookings as $booking) {
                    $pending_requests[] = $booking;
                }
            }
        }
    }
}

// Set page title based on role
$page_title = ucfirst($role) . " Dashboard";

// Include header and navbar
require_once '../app/includes/header.php';
require_once '../app/includes/navbar.php';
?>

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
                    <?php if ($my_bookings && count($my_bookings) > 0): ?>
                        <?php foreach (array_slice($my_bookings, 0, 3) as $booking): ?>
                            <div class="booking-item" style="padding: 15px; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 style="margin: 0; font-size: 1rem;">
                                            <a href="session_view.php?id=<?php echo $booking['session_id']; ?>" style="text-decoration: none; color: #667eea;">
                                                <?php echo escape($booking['session_title']); ?>
                                            </a>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> <?php echo format_datetime($booking['event_datetime']); ?>
                                        </small><br>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> <?php echo escape($booking['instructor_name']); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($booking['status'] === 'accepted'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php elseif ($booking['status'] === 'declined'): ?>
                                            <span class="badge bg-danger">Declined</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($my_bookings) > 3): ?>
                            <div class="text-center mt-3">
                                <a href="my_bookings.php" class="btn btn-sm btn-outline-primary">View All (<?php echo count($my_bookings); ?>)</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p class="text-muted">No bookings to display</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sessions to Rate -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-star"></i> Rate Completed Sessions</h3>
                    <?php if ($sessions_to_rate && count($sessions_to_rate) > 0): ?>
                        <?php foreach (array_slice($sessions_to_rate, 0, 3) as $session): ?>
                            <div class="session-to-rate" style="padding: 15px; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                                <h5 style="margin: 0; font-size: 1rem;"><?php echo escape($session['session_title']); ?></h5>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?php echo escape($session['instructor_name']); ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-check"></i> Completed: <?php echo format_date($session['event_datetime']); ?>
                                </small>
                                <div class="mt-2">
                                    <a href="rate_session.php?session_id=<?php echo $session['session_id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-star"></i> Rate Now
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p class="text-muted">No sessions to rate yet</p>
                            <small>Complete sessions to leave ratings</small>
                        </div>
                    <?php endif; ?>
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
                        <p class="stat-number"><?php echo $instructor_stats['active_sessions'] ?? 0; ?></p>
                        <p class="stat-label">Active Sessions</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number"><?php echo $instructor_stats['total_learners'] ?? 0; ?></p>
                        <p class="stat-label">Total Learners</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-number"><?php echo number_format($instructor_stats['avg_rating'] ?? 0, 1); ?></p>
                        <p class="stat-label">Average Rating</p>
                    </div>
                </div>
            </div>

            <!-- My Sessions -->
            <div class="col-md-8 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-chalkboard-teacher"></i> My Sessions</h3>
                    <?php if ($my_sessions && count($my_sessions) > 0): ?>
                        <?php foreach (array_slice($my_sessions, 0, 3) as $session): ?>
                            <div class="session-item" style="padding: 15px; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 style="margin: 0; font-size: 1rem;">
                                            <a href="session_view.php?id=<?php echo $session['session_id']; ?>" style="text-decoration: none; color: #667eea;">
                                                <?php echo escape($session['title']); ?>
                                            </a>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> <?php echo format_datetime($session['event_datetime']); ?>
                                        </small><br>
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> <?php echo $session['capacity_remaining']; ?>/<?php echo $session['total_capacity']; ?> spots available
                                        </small>
                                    </div>
                                    <div>
                                        <?php if ($session['status'] === 'upcoming'): ?>
                                            <span class="badge bg-primary">Upcoming</span>
                                        <?php elseif ($session['status'] === 'completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($session['status'] === 'cancelled'): ?>
                                            <span class="badge bg-secondary">Cancelled</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="session_edit.php?id=<?php echo $session['session_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($my_sessions) > 3): ?>
                            <div class="text-center mt-3">
                                <a href="my_sessions.php" class="btn btn-sm btn-outline-primary">View All (<?php echo count($my_sessions); ?>)</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-chalkboard"></i>
                            <p>You haven't created any sessions yet.</p>
                            <a href="session_create.php" class="action-btn">
                                <i class="fas fa-plus-circle"></i> Create Your First Session
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Booking Requests -->
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h3><i class="fas fa-bell"></i> Pending Booking Requests</h3>
                    <?php if ($pending_requests && count($pending_requests) > 0): ?>
                        <?php foreach (array_slice($pending_requests, 0, 5) as $request): ?>
                            <div class="booking-request" style="padding: 15px; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 style="margin: 0; font-size: 0.95rem;"><?php echo escape($request['learner_name']); ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-book"></i> <?php echo escape($request['session_title']); ?>
                                        </small><br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> <?php echo time_ago($request['requested_at']); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="booking_manage.php?id=<?php echo $request['booking_id']; ?>&action=accept" class="btn btn-sm btn-success" title="Accept">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="booking_manage.php?id=<?php echo $request['booking_id']; ?>&action=reject" class="btn btn-sm btn-danger" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($pending_requests) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="booking_requests.php" class="btn btn-sm btn-outline-primary">View All (<?php echo count($pending_requests); ?>)</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p class="text-muted">No pending requests</p>
                        </div>
                    <?php endif; ?>
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

<?php require_once '../app/includes/footer.php'; ?>
