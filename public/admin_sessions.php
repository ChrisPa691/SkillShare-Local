<?php
/**
 * Admin Sessions Management Page
 * Allows admins to view, manage, and moderate all sessions
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';
require_once '../app/models/Session.php';

// Require admin login
require_login();
require_role('admin');

// Handle session actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['session_id'])) {
        $session_id = (int)$_POST['session_id'];
        $action = $_POST['action'];
        
        if ($action === 'cancel') {
            if (Session::deleteSession($session_id)) {
                set_flash('Session canceled successfully.', 'success');
            } else {
                set_flash('Failed to cancel session.', 'danger');
            }
        } elseif ($action === 'change_status' && isset($_POST['new_status'])) {
            $new_status = $_POST['new_status'];
            if (in_array($new_status, ['upcoming', 'completed', 'canceled'])) {
                if (Session::updateSession($session_id, ['status' => $new_status])) {
                    set_flash("Session status updated to $new_status successfully.", 'success');
                } else {
                    set_flash('Failed to update session status.', 'danger');
                }
            }
        }
        
        header("Location: admin_sessions.php");
        exit();
    }
}

// Get filters
$status_filter = $_GET['status'] ?? 'all';
$category_filter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build filters array
$filters = [];
if ($status_filter !== 'all') {
    $filters['status'] = $status_filter;
}
if (!empty($category_filter)) {
    $filters['category_id'] = (int)$category_filter;
}
if (!empty($search)) {
    $filters['search'] = $search;
}

// Fetch sessions
$sessions = Session::getAllSessions($filters, 0) ?: [];

// Get categories for filter
$categories = db_select('Categories', [], 0) ?: [];

$page_title = "Manage Sessions";

require_once '../app/includes/header.php';
require_once '../app/includes/navbar.php';
?>

<div class="container mt-5 pt-5">
    <?php display_flash(); ?>
    
    <!-- Header -->
    <div class="dashboard-header mb-4">
        <div class="container">
            <h1><i class="fas fa-chalkboard"></i> Manage Sessions</h1>
            <p class="lead mb-0">View and manage all platform sessions</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            <div class="btn-group" role="group">
                <a href="admin_sessions.php?status=all<?php echo !empty($category_filter) ? '&category=' . $category_filter : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'all' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-list"></i> All (<?php echo count(Session::getAllSessions([], 0) ?: []); ?>)
                </a>
                <a href="admin_sessions.php?status=upcoming<?php echo !empty($category_filter) ? '&category=' . $category_filter : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'upcoming' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-clock"></i> Upcoming (<?php echo db_count('skill_sessions', ['status' => 'upcoming']); ?>)
                </a>
                <a href="admin_sessions.php?status=completed<?php echo !empty($category_filter) ? '&category=' . $category_filter : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'completed' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-check"></i> Completed (<?php echo db_count('skill_sessions', ['status' => 'completed']); ?>)
                </a>
                <a href="admin_sessions.php?status=canceled<?php echo !empty($category_filter) ? '&category=' . $category_filter : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $status_filter === 'canceled' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-ban"></i> Canceled (<?php echo db_count('skill_sessions', ['status' => 'canceled']); ?>)
                </a>
            </div>
        </div>
        
        <div class="col-md-4">
            <select class="form-select" onchange="window.location.href='admin_sessions.php?status=<?php echo $status_filter; ?>&category=' + this.value + '<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>'">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo $category_filter == $cat['category_id'] ? 'selected' : ''; ?>>
                        <?php echo escape($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-8">
            <form method="GET" class="d-flex">
                <input type="hidden" name="status" value="<?php echo escape($status_filter); ?>">
                <?php if (!empty($category_filter)): ?>
                    <input type="hidden" name="category" value="<?php echo escape($category_filter); ?>">
                <?php endif; ?>
                <input type="text" name="search" class="form-control me-2" 
                       placeholder="Search by title or instructor..." value="<?php echo escape($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="card">
        <div class="card-body">
            <?php if (count($sessions) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Instructor</th>
                                <th>Category</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Fee</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td><?php echo $session['session_id']; ?></td>
                                    <td>
                                        <a href="session_view.php?id=<?php echo $session['session_id']; ?>" target="_blank">
                                            <strong><?php echo escape($session['title']); ?></strong>
                                        </a>
                                    </td>
                                    <td><?php echo escape($session['instructor_name']); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo escape($session['category_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_datetime($session['event_datetime']); ?></td>
                                    <td>
                                        <?php if ($session['location_type'] === 'online'): ?>
                                            <span class="badge bg-primary">Online</span>
                                        <?php else: ?>
                                            <?php echo escape($session['city']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($session['fee_type'] === 'free'): ?>
                                            <span class="badge bg-success">Free</span>
                                        <?php else: ?>
                                            $<?php echo number_format($session['fee_amount'], 2); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $remaining = $session['capacity_remaining'] ?? 0;
                                        $total = $session['total_capacity'] ?? 0;
                                        $booked = $total - $remaining;
                                        ?>
                                        <span class="<?php echo $remaining == 0 ? 'text-danger' : ''; ?>">
                                            <?php echo $booked; ?>/<?php echo $total; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $session['status'] === 'upcoming' ? 'primary' : 
                                                 ($session['status'] === 'completed' ? 'success' : 'secondary'); 
                                        ?>">
                                            <?php echo ucfirst($session['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- View -->
                                            <a href="session_view.php?id=<?php echo $session['session_id']; ?>" 
                                               class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Change Status -->
                                            <button type="button" class="btn btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#statusModal<?php echo $session['session_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Cancel -->
                                            <?php if ($session['status'] !== 'canceled'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="btn btn-outline-danger" 
                                                            onclick="return confirm('Are you sure you want to cancel this session?');">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Change Status Modal -->
                                        <div class="modal fade" id="statusModal<?php echo $session['session_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change Session Status</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                                            <input type="hidden" name="action" value="change_status">
                                                            <p><strong>Session:</strong> <?php echo escape($session['title']); ?></p>
                                                            <p><strong>Current Status:</strong> <?php echo ucfirst($session['status']); ?></p>
                                                            <div class="mb-3">
                                                                <label class="form-label">New Status</label>
                                                                <select name="new_status" class="form-select" required>
                                                                    <option value="">Select status...</option>
                                                                    <option value="upcoming" <?php echo $session['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                                                    <option value="completed" <?php echo $session['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                    <option value="canceled" <?php echo $session['status'] === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                                                </select>
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
                    <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                    <h4>No sessions found</h4>
                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="text-center mt-4 mb-5">
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php require_once '../app/includes/footer.php'; ?>
