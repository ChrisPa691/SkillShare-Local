<?php
/**
 * User Profile Page
 * 
 * Displays user profile information and statistics
 */

require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/models/User.php';
require_once '../app/models/UserSettings.php';

// Require authentication
require_login();

$userId = $_SESSION['user_id'];

// Check if viewing another user's profile
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $viewUserId = (int)$_GET['id'];
} else {
    $viewUserId = $userId;
}

// Get user data
$user = User::getById($viewUserId);

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

// Get user settings
$userSettings = UserSettings::get($viewUserId);
if (!$userSettings) {
    UserSettings::create($viewUserId);
    $userSettings = UserSettings::get($viewUserId);
}

// Check if viewing own profile
$isOwnProfile = ($userId === $viewUserId);

// Get user statistics
global $conn;

// Count sessions (for instructors)
$sessionsStmt = $conn->prepare("
    SELECT COUNT(*) as total_sessions,
           SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
           SUM(CASE WHEN status = 'upcoming' THEN 1 ELSE 0 END) as upcoming_sessions
    FROM skill_sessions 
    WHERE instructor_id = :user_id
");
$sessionsStmt->execute(['user_id' => $viewUserId]);
$sessionStats = $sessionsStmt->fetch(PDO::FETCH_ASSOC);

// Count bookings (for learners)
$bookingsStmt = $conn->prepare("
    SELECT COUNT(*) as total_bookings,
           SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_bookings,
           SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings
    FROM bookings 
    WHERE learner_id = :user_id
");
$bookingsStmt->execute(['user_id' => $viewUserId]);
$bookingStats = $bookingsStmt->fetch(PDO::FETCH_ASSOC);

// Get average rating (for instructors)
$ratingStmt = $conn->prepare("
    SELECT AVG(r.rating) as avg_rating, COUNT(r.rating_id) as total_ratings
    FROM ratings r
    JOIN skill_sessions s ON r.session_id = s.session_id
    WHERE s.instructor_id = :user_id
");
$ratingStmt->execute(['user_id' => $viewUserId]);
$ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = $isOwnProfile ? "My Profile" : htmlspecialchars($user['full_name']);
include '../app/includes/header.php';
include '../app/includes/navbar.php';
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="mb-3">
                        <?php if (!empty($user['avatar_path'])): ?>
                            <img src="<?php echo htmlspecialchars($user['avatar_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                 class="rounded-circle" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white" 
                                 style="width: 150px; height: 150px; font-size: 3rem;">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Name and Role -->
                    <h3 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p class="text-muted mb-2">
                        <span class="badge bg-<?php 
                            echo $user['role'] === 'admin' ? 'danger' : 
                                ($user['role'] === 'instructor' ? 'primary' : 'success'); 
                        ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </p>

                    <!-- Location -->
                    <?php if (!empty($user['city'])): ?>
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($user['city']); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Bio -->
                    <?php if (!empty($user['bio'])): ?>
                        <p class="text-muted small mb-3">
                            <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Member Since -->
                    <p class="text-muted small">
                        <i class="fas fa-calendar-alt"></i> Member since 
                        <?php echo date('F Y', strtotime($user['created_at'])); ?>
                    </p>

                    <!-- Account Status -->
                    <div class="mb-3">
                        <?php if ($user['is_suspended']): ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-ban"></i> Account Suspended
                            </span>
                            <?php if (!empty($user['suspended_reason'])): ?>
                                <p class="text-danger small mt-2">
                                    <?php echo htmlspecialchars($user['suspended_reason']); ?>
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <?php if ($isOwnProfile): ?>
                        <div class="d-grid gap-2">
                            <a href="settings.php" class="btn btn-primary">
                                <i class="fas fa-cog"></i> Edit Profile & Settings
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Settings Card (Only for own profile) -->
            <?php if ($isOwnProfile): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-sliders-h"></i> My Preferences</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 small text-muted">Theme:</div>
                            <div class="col-6 small">
                                <span class="badge bg-secondary">
                                    <?php echo ucfirst($userSettings['theme'] ?? 'light'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 small text-muted">Language:</div>
                            <div class="col-6 small">
                                <?php echo strtoupper($userSettings['language'] ?? 'en'); ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 small text-muted">Currency:</div>
                            <div class="col-6 small">
                                <?php echo $userSettings['currency'] ?? 'GBP'; ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 small text-muted">Timezone:</div>
                            <div class="col-6 small text-truncate" title="<?php echo htmlspecialchars($userSettings['timezone'] ?? 'UTC'); ?>">
                                <?php 
                                $tz = $userSettings['timezone'] ?? 'UTC';
                                echo htmlspecialchars(substr($tz, strrpos($tz, '/') + 1)); 
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 small text-muted">Font Size:</div>
                            <div class="col-6 small">
                                <?php echo $userSettings['font_size'] ?? 16; ?>px
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics and Activity -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row">
                <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
                    <!-- Sessions Statistics -->
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="text-primary mb-0"><?php echo $sessionStats['total_sessions'] ?? 0; ?></h2>
                                <p class="text-muted mb-0">Total Sessions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="text-success mb-0"><?php echo $sessionStats['completed_sessions'] ?? 0; ?></h2>
                                <p class="text-muted mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="text-info mb-0"><?php echo $sessionStats['upcoming_sessions'] ?? 0; ?></h2>
                                <p class="text-muted mb-0">Upcoming</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($user['role'] === 'learner' || $user['role'] === 'admin'): ?>
                    <!-- Bookings Statistics -->
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="text-primary mb-0"><?php echo $bookingStats['total_bookings'] ?? 0; ?></h2>
                                <p class="text-muted mb-0">Total Bookings</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="text-success mb-0"><?php echo $bookingStats['accepted_bookings'] ?? 0; ?></h2>
                                <p class="text-muted mb-0">Accepted</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="text-warning mb-0"><?php echo $bookingStats['pending_bookings'] ?? 0; ?></h2>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
                <!-- Rating Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-star"></i> Rating</h5>
                        <?php if ($ratingData['total_ratings'] > 0): ?>
                            <div class="d-flex align-items-center">
                                <div class="display-4 text-warning me-3">
                                    <?php echo number_format($ratingData['avg_rating'], 1); ?>
                                </div>
                                <div>
                                    <div class="mb-1">
                                        <?php 
                                        $avgRating = round($ratingData['avg_rating']);
                                        for ($i = 1; $i <= 5; $i++): 
                                            if ($i <= $avgRating):
                                        ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-warning"></i>
                                        <?php 
                                            endif;
                                        endfor; 
                                        ?>
                                    </div>
                                    <p class="text-muted mb-0">
                                        Based on <?php echo $ratingData['total_ratings']; ?> 
                                        <?php echo $ratingData['total_ratings'] == 1 ? 'review' : 'reviews'; ?>
                                    </p>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No ratings yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Email:</div>
                        <div class="col-sm-8">
                            <?php if ($isOwnProfile): ?>
                                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Hidden for privacy</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Last Updated:</div>
                        <div class="col-sm-8">
                            <?php echo date('F j, Y g:i A', strtotime($user['updated_at'])); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 fw-bold">Account Created:</div>
                        <div class="col-sm-8">
                            <?php echo date('F j, Y g:i A', strtotime($user['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex">
                        <?php if ($isOwnProfile): ?>
                            <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
                                <a href="admin_sessions.php" class="btn btn-outline-primary">
                                    <i class="fas fa-chalkboard-teacher"></i> My Sessions
                                </a>
                            <?php endif; ?>
                            <?php if ($user['role'] === 'learner' || $user['role'] === 'admin'): ?>
                                <a href="my_bookings.php" class="btn btn-outline-success">
                                    <i class="fas fa-bookmark"></i> My Bookings
                                </a>
                            <?php endif; ?>
                            <a href="settings.php" class="btn btn-outline-secondary">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <a href="logout.php" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        <?php else: ?>
                            <a href="sessions.php?instructor=<?php echo $user['user_id']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> View Sessions
                            </a>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Back to Dashboard
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/includes/footer.php'; ?>

<style>
.card {
    border-radius: 8px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 1rem;
}

.card-header {
    background-color: var(--bg-secondary);
    border-bottom: 2px solid var(--border-color);
}

.rounded-circle {
    border: 3px solid var(--border-color);
}
</style>
