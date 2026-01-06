<?php
/**
 * Admin Bookings Management Page
 * Allows admins to view and monitor all bookings
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';
require_once '../app/models/Booking.php';

// Require admin login
require_login();
require_role('admin');

// Handle booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['booking_id'])) {
        $booking_id = (int)$_POST['booking_id'];
        $action = $_POST['action'];
        
        if ($action === 'change_status' && isset($_POST['new_status'])) {
            $new_status = $_POST['new_status'];
            if (in_array($new_status, ['pending', 'accepted', 'declined', 'canceled'])) {
                if (Booking::updateBooking($booking_id, ['status' => $new_status])) {
                    set_flash("Booking status updated to $new_status successfully.", 'success');
                } else {
                    set_flash('Failed to update booking status.', 'danger');
                }
            }
        }
        
        header("Location: admin_bookings.php");
        exit();
    }
}

// Get filters
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Fetch all bookings
global $conn;

$sql = "SELECT b.*, 
               s.title as session_title,
               s.event_datetime,
               s.location_type,
               s.city,
               s.status as session_status,
               l.full_name as learner_name,
               l.email as learner_email,
               i.full_name as instructor_name,
               i.email as instructor_email
        FROM bookings b
        INNER JOIN skill_sessions s ON b.session_id = s.session_id
        INNER JOIN Users l ON b.learner_id = l.user_id
        INNER JOIN Users i ON s.instructor_id = i.user_id
        WHERE 1=1";

$params = [];

// Apply status filter
if ($status_filter !== 'all') {
    $sql .= " AND b.status = ?";
    $params[] = $status_filter;
}

// Apply search filter
if (!empty($search)) {
    $sql .= " AND (s.title LIKE ? OR l.full_name LIKE ? OR i.full_name LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$sql .= " ORDER BY b.requested_at DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching bookings: " . $e->getMessage());
    $bookings = [];
}

// Get statistics
$overall_stats = Booking::getOverallStats();
$monthly_stats = Booking::getBookingsPerMonth(6); // Last 6 months

$page_title = "View Bookings";

require_once '../app/includes/header.php';
require_once '../app/includes/navbar.php';
?>

<div class="container mt-5 pt-5">
    <?php 
    display_breadcrumbs([
        ['label' => 'Admin', 'url' => 'dashboard.php'],
        ['label' => 'View All Bookings', 'icon' => 'bookmark']
    ]);
    display_flash(); 
    ?>
    
    <!-- Header -->
    <div class="dashboard-header mb-4">
        <div class="container">
            <h1><i class="fas fa-bookmark"></i> View All Bookings</h1>
            <p class="lead mb-0">Monitor all session bookings on the platform</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="btn-group" role="group">
                <a href="admin_bookings.php?status=all<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'all' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-list"></i> All (<?php echo db_count('bookings'); ?>)
                </a>
                <a href="admin_bookings.php?status=pending<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'pending' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-clock"></i> Pending (<?php echo db_count('bookings', ['status' => 'pending']); ?>)
                </a>
                <a href="admin_bookings.php?status=accepted<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'accepted' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-check"></i> Accepted (<?php echo db_count('bookings', ['status' => 'accepted']); ?>)
                </a>
                <a href="admin_bookings.php?status=declined<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'declined' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-times"></i> Declined (<?php echo db_count('bookings', ['status' => 'declined']); ?>)
                </a>
                <a href="admin_bookings.php?status=canceled<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'canceled' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-ban"></i> Canceled (<?php echo db_count('bookings', ['status' => 'canceled']); ?>)
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <form method="GET" class="d-flex">
                <input type="hidden" name="status" value="<?php echo escape($status_filter); ?>">
                <input type="text" name="search" class="form-control me-2" 
                       placeholder="Search bookings..." value="<?php echo escape($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-body">
            <?php if (count($bookings) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Session</th>
                                <th>Learner</th>
                                <th>Instructor</th>
                                <th>Date & Time</th>
                                <th>Booked</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['booking_id']; ?></td>
                                    <td>
                                        <a href="session_view.php?id=<?php echo $booking['session_id']; ?>" target="_blank">
                                            <strong><?php echo escape($booking['session_title']); ?></strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            <?php if ($booking['location_type'] === 'online'): ?>
                                                <i class="fas fa-laptop"></i> Online
                                            <?php else: ?>
                                                <i class="fas fa-map-marker-alt"></i> <?php echo escape($booking['city']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php echo escape($booking['learner_name']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo escape($booking['learner_email']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo escape($booking['instructor_name']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo escape($booking['instructor_email']); ?></small>
                                    </td>
                                    <td><?php echo format_datetime($booking['event_datetime']); ?></td>
                                    <td><?php echo time_ago($booking['requested_at']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $booking['status'] === 'pending' ? 'warning text-dark' : 
                                                 ($booking['status'] === 'accepted' ? 'success' : 
                                                 ($booking['status'] === 'declined' ? 'danger' : 'secondary')); 
                                        ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                        <?php if ($booking['status'] === 'declined' && !empty($booking['rejection_reason'])): ?>
                                            <br>
                                            <small class="text-danger" title="<?php echo escape($booking['rejection_reason']); ?>">
                                                <i class="fas fa-info-circle"></i> Reason provided
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- View Session -->
                                            <a href="session_view.php?id=<?php echo $booking['session_id']; ?>" 
                                               class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Change Status -->
                                            <button type="button" class="btn btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#statusModal<?php echo $booking['booking_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Change Status Modal -->
                                        <div class="modal fade" id="statusModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change Booking Status</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                            <input type="hidden" name="action" value="change_status">
                                                            <p><strong>Session:</strong> <?php echo escape($booking['session_title']); ?></p>
                                                            <p><strong>Learner:</strong> <?php echo escape($booking['learner_name']); ?></p>
                                                            <p><strong>Current Status:</strong> <?php echo ucfirst($booking['status']); ?></p>
                                                            <div class="mb-3">
                                                                <label class="form-label">New Status</label>
                                                                <select name="new_status" class="form-select" required>
                                                                    <option value="">Select status...</option>
                                                                    <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="accepted" <?php echo $booking['status'] === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                                                    <option value="declined" <?php echo $booking['status'] === 'declined' ? 'selected' : ''; ?>>Declined</option>
                                                                    <option value="canceled" <?php echo $booking['status'] === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                                                </select>
                                                            </div>
                                                            <div class="alert alert-warning">
                                                                <small><i class="fas fa-exclamation-triangle"></i> Changing status directly may not update session capacity.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update Status</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                    <h4>No bookings found</h4>
                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3"><i class="fas fa-chart-bar"></i> Booking Statistics</h3>
        </div>
    </div>
    
    <!-- Overall Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-primary"><?php echo $overall_stats['total_bookings'] ?? 0; ?></h4>
                    <p class="text-muted mb-0 small">Total Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-warning"><?php echo $overall_stats['pending'] ?? 0; ?></h4>
                    <p class="text-muted mb-0 small">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-success"><?php echo $overall_stats['accepted'] ?? 0; ?></h4>
                    <p class="text-muted mb-0 small">Accepted</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-danger"><?php echo $overall_stats['declined'] ?? 0; ?></h4>
                    <p class="text-muted mb-0 small">Declined</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-secondary"><?php echo $overall_stats['canceled'] ?? 0; ?></h4>
                    <p class="text-muted mb-0 small">Canceled</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-info">
                        <?php 
                        $total = $overall_stats['total_bookings'] ?? 0;
                        $accepted = $overall_stats['accepted'] ?? 0;
                        echo $total > 0 ? round(($accepted / $total) * 100) : 0;
                        ?>%
                    </h4>
                    <p class="text-muted mb-0 small">Accept Rate</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Breakdown -->
    <?php if (!empty($monthly_stats)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Monthly Breakdown (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center text-warning">Pending</th>
                                    <th class="text-center text-success">Accepted</th>
                                    <th class="text-center text-danger">Declined</th>
                                    <th class="text-center text-secondary">Canceled</th>
                                    <th class="text-center">Accept Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthly_stats as $stat): ?>
                                <tr>
                                    <td><strong><?php echo escape($stat['month_label']); ?></strong></td>
                                    <td class="text-center"><?php echo $stat['total']; ?></td>
                                    <td class="text-center text-warning"><?php echo $stat['pending']; ?></td>
                                    <td class="text-center text-success"><?php echo $stat['accepted']; ?></td>
                                    <td class="text-center text-danger"><?php echo $stat['declined']; ?></td>
                                    <td class="text-center text-secondary"><?php echo $stat['canceled']; ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $month_total = $stat['total'];
                                        $month_accepted = $stat['accepted'];
                                        $rate = $month_total > 0 ? round(($month_accepted / $month_total) * 100) : 0;
                                        echo $rate . '%';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Back to Dashboard -->
    <div class="text-center mt-4 mb-5">
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php require_once '../app/includes/footer.php'; ?>
