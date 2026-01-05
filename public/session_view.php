<?php
/**
 * Session Detail View Page
 * Display detailed information about a specific session
 * Allow learners to book the session
 */

session_start();

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Rating.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/includes/helpers.php';
require_once __DIR__ . '/../app/includes/auth_guard.php';

// Get session ID from URL parameter
$session_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($session_id <= 0) {
    set_message('error', 'Invalid session ID.');
    redirect('sessions.php');
    exit;
}

// Get session details
$session = Session::getSessionById($session_id);
if (!$session) {
    set_message('error', 'Session not found.');
    redirect('sessions.php');
    exit;
}

// Get instructor details
$instructor = User::getUserById($session['instructor_id']);

// Get ratings for this session
$ratings = Rating::getRatingsBySession($session_id);

// Check if current user already booked this session
$user_booking = null;
if (is_logged_in()) {
    $user_booking = Booking::getBookingByLearnerAndSession($_SESSION['user_id'], $session_id);
}

// Determine booking eligibility
$can_book = false;
if (is_logged_in() && get_user_role() === 'learner') {
    if (!$user_booking && $session['capacity_remaining'] > 0 && $session['status'] === 'upcoming') {
        $can_book = true;
    }
}

// Check if session is full
$is_full = ($session['capacity_remaining'] <= 0);

// Check if session is past
$is_past = (strtotime($session['event_datetime']) < time());

// Page metadata
$page_title = $session['title'] ?? 'Session Details';
$page = 'sessions';

require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/includes/navbar.php';
?>

<div class="container my-5 session-view-page">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="sessions.php">Sessions</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= escape($session['title']) ?>
            </li>
        </ol>
    </nav>

    <div class="row">
        
        <!-- Main Content -->
        <div class="col-lg-8">
            
            <!-- Session Header -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    
                    <!-- Session Title -->
                    <h1 class="h2 mb-3"><?= escape($session['title']) ?></h1>
                    
                    <!-- Category Badge -->
                    <span class="badge bg-primary mb-3"><?= escape($session['category_name']) ?></span>
                    
                    <!-- Session Image -->
                    <?php if (!empty($session['photo_url'])): ?>
                        <img src="<?= escape($session['photo_url']) ?>" alt="<?= escape($session['title']) ?>" class="img-fluid rounded mb-3">
                    <?php endif; ?>
                    
                    <!-- Description -->
                    <h3 class="h5 mt-4">About This Session</h3>
                    <p><?= nl2br(escape($session['description'])) ?></p>
                    
                    <!-- Sustainability Information -->
                    <?php if (!empty($session['sustainability_description'])): ?>
                        <h3 class="h5 mt-4">Sustainability Impact</h3>
                        <div class="alert alert-success">
                            <i class="fas fa-leaf me-2"></i>
                            <?= nl2br(escape($session['sustainability_description'])) ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>

            <!-- Ratings Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">
                        <i class="fas fa-star me-2"></i>Ratings & Reviews
                    </h3>
                    
                    <!-- Rating Summary -->
                    <?php if (!empty($ratings)): ?>
                        <?php 
                        $total_ratings = count($ratings);
                        $average_rating = $session['average_rating'];
                        ?>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="text-warning me-3" style="font-size: 2rem;">
                                    <?php 
                                    $rating_rounded = round($average_rating);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating_rounded ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                                <div>
                                    <h4 class="mb-0"><?= number_format($average_rating, 1) ?></h4>
                                    <p class="text-muted small mb-0"><?= $total_ratings ?> rating(s)</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No ratings yet.</p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Individual Reviews -->
                    <?php if (!empty($ratings)): ?>
                        <?php foreach ($ratings as $rating): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong><?= escape($rating['learner_name']) ?></strong>
                                    <small class="text-muted"><?= date('M d, Y', strtotime($rating['created_at'])) ?></small>
                                </div>
                                <div class="text-warning">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating['rating'] ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                                <?php if (!empty($rating['comment'])): ?>
                                    <p class="mt-2 mb-0"><?= nl2br(escape($rating['comment'])) ?></p>
                                <?php endif; ?>
                            </div>
                            <hr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Session Info Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    
                    <!-- Fee -->
                    <h3 class="h5 mb-3">Session Fee</h3>
                    <?php if ($session['fee_type'] === 'free'): ?>
                        <p class="h4 text-success">Free</p>
                    <?php else: ?>
                        <p class="h4 text-primary">$<?= number_format($session['fee_amount'], 2) ?></p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Date and Time -->
                    <h3 class="h5 mb-3">When</h3>
                    <p><i class="fas fa-calendar me-2"></i><?= date('l, F j, Y', strtotime($session['event_datetime'])) ?></p>
                    <p><i class="fas fa-clock me-2"></i><?= date('g:i A', strtotime($session['event_datetime'])) ?></p>
                    <p><i class="fas fa-hourglass-half me-2"></i><?= $session['duration_minutes'] ?> minutes</p>
                    
                    <hr>
                    
                    <!-- Location -->
                    <h3 class="h5 mb-3">Where</h3>
                    <?php if ($session['location_type'] === 'online'): ?>
                        <p><i class="fas fa-video me-2"></i>Online Session</p>
                        <small class="text-muted">Meeting link will be provided after booking</small>
                    <?php else: ?>
                        <p><i class="fas fa-map-marker-alt me-2"></i><?= escape($session['city']) ?></p>
                        <p class="text-muted small"><?= escape($session['address']) ?></p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Capacity -->
                    <h3 class="h5 mb-3">Availability</h3>
                    <p><?= $session['capacity_remaining'] ?> / <?= $session['total_capacity'] ?> seats available</p>
                    <?php 
                    $capacity_percentage = $session['total_capacity'] > 0 ? (($session['total_capacity'] - $session['capacity_remaining']) / $session['total_capacity']) * 100 : 0;
                    ?>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: <?= $capacity_percentage ?>%" 
                             aria-valuenow="<?= $capacity_percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <hr>
                    
                    <!-- Booking Button -->
                    <?php if ($user_booking): ?>
                        <?php if ($user_booking['status'] === 'pending'): ?>
                            <button class="btn btn-warning w-100" disabled>
                                <i class="fas fa-clock me-2"></i>Booking Pending
                            </button>
                        <?php elseif ($user_booking['status'] === 'accepted'): ?>
                            <button class="btn btn-success w-100" disabled>
                                <i class="fas fa-check me-2"></i>Booking Confirmed
                            </button>
                        <?php elseif ($user_booking['status'] === 'declined'): ?>
                            <button class="btn btn-danger w-100" disabled>
                                <i class="fas fa-times me-2"></i>Booking Declined
                            </button>
                        <?php endif; ?>
                    <?php elseif ($can_book): ?>
                        <form method="POST" action="book_session.php">
                            <input type="hidden" name="session_id" value="<?= $session_id ?>">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-calendar-plus me-2"></i>Book This Session
                            </button>
                        </form>
                    <?php elseif ($is_full): ?>
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-users me-2"></i>Session Full
                        </button>
                    <?php elseif ($is_past): ?>
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-calendar-check me-2"></i>Session Completed
                        </button>
                    <?php elseif (!is_logged_in()): ?>
                        <a href="login.php?redirect=<?= urlencode('session_view.php?id=' . $session_id) ?>" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>
                            Only Learners Can Book
                        </button>
                    <?php endif; ?>
                    
                </div>
            </div>

            <!-- Instructor Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">Your Instructor</h3>
                    
                    <div class="d-flex align-items-center mb-3">
                        <?php if (!empty($instructor['profile_picture'])): ?>
                            <img src="<?= escape($instructor['profile_picture']) ?>" alt="Instructor" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <h5 class="mb-0"><?= escape($instructor['full_name']) ?></h5>
                            <p class="text-muted small mb-0"><?= escape($instructor['city']) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($instructor['bio'])): ?>
                        <p class="mt-3"><?= nl2br(escape($instructor['bio'])) ?></p>
                    <?php endif; ?>
                    
                    <?php 
                    $instructor_stats = Session::getInstructorStats($instructor['user_id']);
                    ?>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <h6><?= $instructor_stats['total_sessions'] ?></h6>
                            <small class="text-muted">Sessions</small>
                        </div>
                        <div class="col-6">
                            <h6><?= number_format($instructor_stats['average_rating'], 1) ?></h6>
                            <small class="text-muted">Rating</small>
                        </div>
                    </div>
                    
                </div>
            </div>

            <!-- Share Card (Optional Enhancement) -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">Share This Session</h3>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($session['title']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($session['title'] . ' - http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<!-- Session View Scripts -->
<script src="assets/js/sessions.js"></script>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>
