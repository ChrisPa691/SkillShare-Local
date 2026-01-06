<?php
/**
 * My Bookings/Sessions Page
 * Displays bookings for learners or sessions for instructors
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';
require_once '../app/models/Booking.php';
require_once '../app/models/Session.php';

// Require login
require_login();

$user_id = get_user_id();
$user_role = get_user_role();

// Get filter from query string
$filter = $_GET['filter'] ?? 'all';

// Fetch data based on user role
if ($user_role === 'instructor') {
    // Instructors see their sessions
    if ($filter === 'all') {
        $sessions = Session::getSessionsByInstructor($user_id);
    } else {
        $sessions = Session::getSessionsByInstructor($user_id, $filter);
    }
    $page_title = "My Sessions";
} else {
    // Learners see their bookings
    if ($filter === 'all') {
        $bookings = Booking::getBookingsByLearner($user_id);
    } else {
        $bookings = Booking::getBookingsByLearner($user_id, $filter);
    }
    $page_title = "My Bookings";
}

require_once '../app/includes/header.php';
require_once '../app/includes/navbar.php';
?>

<div class="container mt-5 pt-5">
    <?php 
    if ($user_role === 'instructor') {
        display_breadcrumbs([
            ['label' => 'My Sessions', 'icon' => 'chalkboard-teacher']
        ]);
    } else {
        display_breadcrumbs([
            ['label' => 'My Bookings', 'icon' => 'bookmark']
        ]);
    }
    ?>
    
    <!-- Header -->
    <div class="bookings-header">
        <div class="container text-center">
            <?php if ($user_role === 'instructor'): ?>
                <h1><i class="fas fa-chalkboard-teacher me-3"></i>My Sessions</h1>
                <p class="lead mb-0">Manage your teaching sessions</p>
            <?php else: ?>
                <h1><i class="fas fa-bookmark me-3"></i>My Bookings</h1>
                <p class="lead mb-0">Manage your session bookings</p>
            <?php endif; ?>
        </div>
    </div>

    <?php display_flash(); ?>

    <!-- Filter Tabs -->
    <div class="filter-tabs text-center">
        <?php if ($user_role === 'instructor'): ?>
            <a href="my_bookings.php?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">
                <i class="fas fa-list"></i> All
            </a>
            <a href="my_bookings.php?filter=upcoming" class="btn btn-outline-primary <?php echo $filter === 'upcoming' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Upcoming
            </a>
            <a href="my_bookings.php?filter=completed" class="btn btn-outline-success <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                <i class="fas fa-check"></i> Completed
            </a>
            <a href="my_bookings.php?filter=canceled" class="btn btn-outline-danger <?php echo $filter === 'canceled' ? 'active' : ''; ?>">
                <i class="fas fa-times"></i> Canceled
            </a>
        <?php else: ?>
            <a href="my_bookings.php?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">
                <i class="fas fa-list"></i> All
            </a>
            <a href="my_bookings.php?filter=pending" class="btn btn-outline-warning <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="my_bookings.php?filter=accepted" class="btn btn-outline-success <?php echo $filter === 'accepted' ? 'active' : ''; ?>">
                <i class="fas fa-check"></i> Confirmed
            </a>
            <a href="my_bookings.php?filter=declined" class="btn btn-outline-danger <?php echo $filter === 'declined' ? 'active' : ''; ?>">
                <i class="fas fa-times"></i> Declined
            </a>
        <?php endif; ?>
    </div>

    <!-- Bookings/Sessions List -->
    <div class="row">
        <?php if ($user_role === 'instructor'): ?>
            <?php if ($sessions && count($sessions) > 0): ?>
                <?php foreach ($sessions as $session): ?>
                    <div class="col-md-6 mb-4">
                        <div class="booking-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h4 class="booking-title">
                                    <a href="session_view.php?id=<?php echo $session['session_id']; ?>">
                                        <?php echo escape($session['title']); ?>
                                    </a>
                                </h4>
                                <div>
                                    <?php if ($session['status'] === 'upcoming'): ?>
                                        <span class="badge bg-primary badge-status">
                                            <i class="fas fa-calendar-alt"></i> Upcoming
                                        </span>
                                    <?php elseif ($session['status'] === 'completed'): ?>
                                        <span class="badge bg-success badge-status">
                                            <i class="fas fa-check"></i> Completed
                                        </span>
                                    <?php elseif ($session['status'] === 'canceled'): ?>
                                        <span class="badge bg-danger badge-status">
                                            <i class="fas fa-times"></i> Canceled
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-tag text-primary"></i>
                                    <strong>Category:</strong> <?php echo escape($session['category_name']); ?>
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-calendar text-primary"></i>
                                    <strong>Date & Time:</strong> <?php echo format_datetime($session['event_datetime']); ?>
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-clock text-primary"></i>
                                    <strong>Duration:</strong> <?php echo $session['duration_minutes']; ?> minutes
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-users text-primary"></i>
                                    <strong>Capacity:</strong> <?php echo $session['capacity_remaining']; ?> / <?php echo $session['total_capacity']; ?> remaining
                                </p>
                                <p style="margin: 5px 0;">
                                    <i class="fas fa-star text-warning"></i>
                                    <strong>Rating:</strong> <?php echo number_format($session['average_rating'], 1); ?> / 5.0
                                </p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="session_view.php?id=<?php echo $session['session_id']; ?>" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="session_edit.php?id=<?php echo $session['session_id']; ?>" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No sessions found</h3>
                        <p>You haven't created any sessions yet.</p>
                        <a href="session_create.php" class="btn btn-lg btn-primary mt-3">
                            <i class="fas fa-plus"></i> Create Session
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($bookings && count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6 mb-4">
                    <div class="booking-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 class="booking-title">
                                <a href="session_view.php?id=<?php echo $booking['session_id']; ?>">
                                    <?php echo escape($booking['session_title']); ?>
                                </a>
                            </h4>
                            <div>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark badge-status">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php elseif ($booking['status'] === 'accepted'): ?>
                                    <span class="badge bg-success badge-status">
                                        <i class="fas fa-check"></i> Confirmed
                                    </span>
                                <?php elseif ($booking['status'] === 'declined'): ?>
                                    <span class="badge bg-danger text-white badge-status">
                                        <i class="fas fa-times"></i> Declined
                                    </span>
                                <?php elseif ($booking['status'] === 'cancelled'): ?>
                                    <span class="badge bg-secondary badge-status">
                                        <i class="fas fa-ban"></i> Cancelled
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p style="margin: 5px 0;">
                                <i class="fas fa-user text-primary"></i>
                                <strong>Instructor:</strong> <?php echo escape($booking['instructor_name']); ?>
                            </p>
                            <p style="margin: 5px 0;">
                                <i class="fas fa-calendar text-primary"></i>
                                <strong>Date & Time:</strong> <?php echo format_datetime($booking['event_datetime']); ?>
                            </p>
                            <p style="margin: 5px 0;">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                <strong>Location:</strong> 
                                <?php if ($booking['location_type'] === 'in-person'): ?>
                                    <?php echo escape($booking['address'] ?? $booking['city']); ?>
                                <?php else: ?>
                                    <span class="badge bg-info">Online</span>
                                <?php endif; ?>
                            </p>
                            <p style="margin: 5px 0;">
                                <i class="fas fa-clock text-primary"></i>
                                <strong>Booked:</strong> <?php echo time_ago($booking['requested_at']); ?>
                            </p>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="session_view.php?id=<?php echo $booking['session_id']; ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View Session
                            </a>
                            
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" action="booking_cancel.php" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] === 'accepted'): ?>
                                <?php 
                                // Calculate session end time (event_datetime + duration)
                                $session_end_time = strtotime($booking['event_datetime']) + ($booking['duration_minutes'] * 60);
                                $session_ended = $session_end_time < time();
                                ?>
                                <a href="rate_session.php?session_id=<?php echo $booking['session_id']; ?>" 
                                   class="btn btn-warning <?php echo !$session_ended ? 'disabled' : ''; ?>"
                                   <?php echo !$session_ended ? 'aria-disabled="true" onclick="return false;" title="Available after session ends"' : ''; ?>>
                                    <i class="fas fa-star"></i> Rate Session
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($booking['rejection_reason']) && $booking['status'] === 'declined'): ?>
                            <div class="alert alert-danger mt-3" style="margin-bottom: 0;">
                                <strong>Decline Reason:</strong> <?php echo escape($booking['rejection_reason']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No bookings found</h3>
                    <p>You haven't booked any sessions yet.</p>
                    <a href="sessions.php" class="btn btn-lg btn-primary mt-3">
                        <i class="fas fa-search"></i> Browse Sessions
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Back to Dashboard -->
    <div class="text-center mt-4 mb-5">
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php require_once '../app/includes/footer.php'; ?>
