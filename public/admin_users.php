<?php
/**
 * Admin Users Management Page
 * Allows admins to view, manage, and moderate all users
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/includes/helpers.php';
require_once '../app/models/User.php';

// Require admin login
require_login();
require_role('admin');

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        if ($action === 'suspend' && isset($_POST['reason'])) {
            $reason = trim($_POST['reason']);
            if (User::suspendUser($user_id, $reason)) {
                set_flash('User suspended successfully.', 'success');
            } else {
                set_flash('Failed to suspend user.', 'danger');
            }
        } elseif ($action === 'unsuspend') {
            if (User::unsuspendUser($user_id)) {
                set_flash('User unsuspended successfully.', 'success');
            } else {
                set_flash('Failed to unsuspend user.', 'danger');
            }
        } elseif ($action === 'change_role' && isset($_POST['new_role'])) {
            $new_role = $_POST['new_role'];
            if (in_array($new_role, ['learner', 'instructor', 'admin'])) {
                if (User::updateUser($user_id, ['role' => $new_role])) {
                    set_flash("User role updated to $new_role successfully.", 'success');
                } else {
                    set_flash('Failed to update user role.', 'danger');
                }
            }
        }
        
        header("Location: admin_users.php");
        exit();
    }
}

// Get filter
$role_filter = $_GET['role'] ?? 'all';
$search = $_GET['search'] ?? '';

// Fetch users
if ($role_filter === 'all') {
    $users = db_select('Users', [], 0) ?: [];
} else {
    $users = User::getUsersByRole($role_filter) ?: [];
}

// Apply search filter
if (!empty($search)) {
    $users = array_filter($users, function($user) use ($search) {
        return stripos($user['full_name'], $search) !== false || 
               stripos($user['email'], $search) !== false ||
               stripos($user['city'], $search) !== false;
    });
}

// Sort by created_at descending
usort($users, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$page_title = "Manage Users";

require_once '../app/includes/header.php';
require_once '../app/includes/navbar.php';
?>

<div class="container mt-5 pt-5">
    <?php 
    display_breadcrumbs([
        ['label' => 'Admin', 'url' => 'dashboard.php'],
        ['label' => 'Manage Users', 'icon' => 'users-cog']
    ]);
    display_flash(); 
    ?>
    
    <!-- Header -->
    <div class="dashboard-header mb-4">
        <div class="container">
            <h1><i class="fas fa-users-cog"></i> Manage Users</h1>
            <p class="lead mb-0">View and manage all platform users</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="btn-group" role="group">
                <a href="admin_users.php?role=all<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $role_filter === 'all' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-users"></i> All (<?php echo count(db_select('Users', [], 0) ?: []); ?>)
                </a>
                <a href="admin_users.php?role=learner<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $role_filter === 'learner' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-user-graduate"></i> Learners (<?php echo db_count('Users', ['role' => 'learner']); ?>)
                </a>
                <a href="admin_users.php?role=instructor<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $role_filter === 'instructor' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Instructors (<?php echo db_count('Users', ['role' => 'instructor']); ?>)
                </a>
                <a href="admin_users.php?role=admin<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn btn-<?php echo $role_filter === 'admin' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-user-shield"></i> Admins (<?php echo db_count('Users', ['role' => 'admin']); ?>)
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <form method="GET" class="d-flex">
                <input type="hidden" name="role" value="<?php echo escape($role_filter); ?>">
                <input type="text" name="search" class="form-control me-2" 
                       placeholder="Search users..." value="<?php echo escape($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="<?php echo $user['is_suspended'] ? 'table-danger' : ''; ?>">
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td>
                                        <strong><?php echo escape($user['full_name']); ?></strong>
                                    </td>
                                    <td><?php echo escape($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $user['role'] === 'admin' ? 'danger' : 
                                                 ($user['role'] === 'instructor' ? 'success' : 'primary'); 
                                        ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo escape($user['city'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($user['is_suspended']): ?>
                                            <span class="badge bg-danger" title="<?php echo escape($user['suspended_reason']); ?>">
                                                Suspended
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo time_ago($user['created_at']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Change Role -->
                                            <button type="button" class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#roleModal<?php echo $user['user_id']; ?>">
                                                <i class="fas fa-user-tag"></i>
                                            </button>
                                            
                                            <!-- Suspend/Unsuspend -->
                                            <?php if (!$user['is_suspended']): ?>
                                                <button type="button" class="btn btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#suspendModal<?php echo $user['user_id']; ?>">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <input type="hidden" name="action" value="unsuspend">
                                                    <button type="submit" class="btn btn-outline-success" 
                                                            onclick="return confirm('Unsuspend this user?');">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Change Role Modal -->
                                        <div class="modal fade" id="roleModal<?php echo $user['user_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change User Role</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            <input type="hidden" name="action" value="change_role">
                                                            <p><strong>User:</strong> <?php echo escape($user['full_name']); ?></p>
                                                            <p><strong>Current Role:</strong> <?php echo ucfirst($user['role']); ?></p>
                                                            <div class="mb-3">
                                                                <label class="form-label">New Role</label>
                                                                <select name="new_role" class="form-select" required>
                                                                    <option value="">Select role...</option>
                                                                    <option value="learner" <?php echo $user['role'] === 'learner' ? 'selected' : ''; ?>>Learner</option>
                                                                    <option value="instructor" <?php echo $user['role'] === 'instructor' ? 'selected' : ''; ?>>Instructor</option>
                                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update Role</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Suspend Modal -->
                                        <div class="modal fade" id="suspendModal<?php echo $user['user_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Suspend User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            <input type="hidden" name="action" value="suspend">
                                                            <p><strong>User:</strong> <?php echo escape($user['full_name']); ?></p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Suspension Reason</label>
                                                                <textarea name="reason" class="form-control" rows="3" required 
                                                                          placeholder="Enter reason for suspension..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Suspend User</button>
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
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>No users found</h4>
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
