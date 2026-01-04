<?php
/**
 * My Bookings Page
 * Displays all bookings for the logged-in learner
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';
require_once '../app/models/Booking.php';

// Require learner login
require_login();

$user_id = get_user_id();
$user_role = get_user_role();

// Get filter from query string
$filter = $_GET['filter'] ?? 'all';

// Fetch bookings based on filter
if ($filter === 'all') {
    $bookings = Booking::getBookingsByLearner($user_id);
} else {
    $bookings = Booking::getBookingsByLearner($user_id, $filter);
}

$page_title = "My Bookings";

include '../app/includes/header.php';
include '../app/includes/navbar.php';
?>

<style>
    .bookings-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .booking-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .filter-tabs {
        margin-bottom: 30px;
    }
    
    .filter-tabs .btn {
        margin: 5px;
        border-radius: 25px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .filter-tabs .btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>

<div class="container mt-5 pt-5">
    <!-- Header -->
    <div class="bookings-header">
        <div class="container text-center">
            <h1><i class="fas fa-bookmark me-3"></i>My Bookings</h1>
            <p class="lead mb-0">Manage your session bookings</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs text-center">
        <a href="my_bookings.php?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> All
        </a>
        <a href="my_bookings.php?filter=pending" class="btn btn-outline-warning <?php echo $filter === 'pending' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Pending
        </a>
        <a href="my_bookings.php?filter=accepted" class="btn btn-outline-success <?php echo $filter === 'accepted' ? 'active' : ''; ?>">
            <i class="fas fa-check"></i> Confirmed
        </a>
        <a href="my_bookings.php?filter=rejected" class="btn btn-outline-danger <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
            <i class="fas fa-times"></i> Rejected
        </a>
    </div>

    <!-- Bookings List -->
    <div class="row">
        <?php if ($bookings && count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6 mb-4">
                    <div class="booking-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 style="margin: 0; color: #667eea;">
                                <a href="session_view.php?id=<?php echo $booking['session_id']; ?>" 
                                   style="text-decoration: none; color: inherit;">
                                    <?php echo escape($booking['session_title']); ?>
                                </a>
                            </h4>
                            <div>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark" style="font-size: 1rem; padding: 8px 15px;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php elseif ($booking['status'] === 'accepted'): ?>
                                    <span class="badge bg-success" style="font-size: 1rem; padding: 8px 15px;">
                                        <i class="fas fa-check"></i> Confirmed
                                    </span>
                                <?php elseif ($booking['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger" style="font-size: 1rem; padding: 8px 15px;">
                                        <i class="fas fa-times"></i> Rejected
                                    </span>
                                <?php elseif ($booking['status'] === 'cancelled'): ?>
                                    <span class="badge bg-secondary" style="font-size: 1rem; padding: 8px 15px;">
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
                                <strong>Booked:</strong> <?php echo time_ago($booking['booked_at']); ?>
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
                            
                            <?php if ($booking['status'] === 'accepted' && $booking['session_status'] === 'completed'): ?>
                                <a href="rate_session.php?session_id=<?php echo $booking['session_id']; ?>" 
                                   class="btn btn-warning">
                                    <i class="fas fa-star"></i> Rate Session
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($booking['rejection_reason'] && $booking['status'] === 'rejected'): ?>
                            <div class="alert alert-danger mt-3" style="margin-bottom: 0;">
                                <strong>Rejection Reason:</strong> <?php echo escape($booking['rejection_reason']); ?>
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
    </div>
    
    <!-- Back to Dashboard -->
    <div class="text-center mt-4 mb-5">
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php include '../app/includes/footer.php'; ?>
