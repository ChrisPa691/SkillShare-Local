<?php
/**
 * Rate Session Page
 * Allows learners to rate and review completed sessions
 */

session_start();
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/controllers/RatingController.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/models/Rating.php';
require_once __DIR__ . '/../app/includes/helpers.php';
require_once __DIR__ . '/../app/includes/auth_guard.php';

// Require learner login
require_login();
require_role('learner');

// Get session ID
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

if ($session_id <= 0) {
    set_flash('error', 'Invalid session ID.');
    redirect('my_bookings.php');
    exit;
}

// Get session details
$session = Session::getSessionById($session_id);
if (!$session) {
    set_flash('error', 'Session not found.');
    redirect('my_bookings.php');
    exit;
}

// Verify user has attended this session
$booking = Booking::getBookingByLearnerAndSession($_SESSION['user_id'], $session_id);
if (!$booking || $booking['status'] !== 'accepted') {
    set_flash('error', 'You can only rate sessions you attended.');
    redirect('my_bookings.php');
    exit;
}

// Check if session has ended (event_datetime + duration has passed)
$session_end_time = strtotime($session['event_datetime']) + ($session['duration_minutes'] * 60);
$current_time = time();

if ($current_time < $session_end_time) {
    set_flash('error', 'You can only rate sessions after they have ended.');
    redirect('my_bookings.php');
    exit;
}

// Check if rating already exists
$existing_rating = Rating::getRatingByLearnerAndSession($_SESSION['user_id'], $session_id);
if ($existing_rating) {
    set_flash('warning', 'You have already rated this session.');
    redirect('my_bookings.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = RatingController::submitRating();
    
    if ($result['success']) {
        set_flash('success', $result['message']);
        redirect('my_bookings.php');
    } else {
        set_flash('error', $result['message']);
    }
}

$page_title = "Rate Session";
require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-star text-warning"></i> Rate Session
                </h1>
                <a href="my_bookings.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to My Bookings
                </a>
            </div>

            <?php display_flash(); ?>

            <!-- Session Info Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h3 class="h5"><?= escape($session['title']) ?></h3>
                    <p class="text-muted mb-0">
                        <i class="fas fa-user"></i> Instructor: <?= escape($session['instructor_name']) ?><br>
                        <i class="fas fa-calendar"></i> <?= date('F j, Y', strtotime($session['event_datetime'])) ?>
                    </p>
                </div>
            </div>

            <!-- Rating Form -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="rate_session.php?session_id=<?= $session_id ?>">
                        <input type="hidden" name="session_id" value="<?= $session_id ?>">
                        
                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <div class="btn-group" role="group" aria-label="Rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" class="btn-check" name="rating" id="rating<?= $i ?>" value="<?= $i ?>" required>
                                        <label class="btn btn-outline-warning" for="rating<?= $i ?>">
                                            <?php for ($j = 1; $j <= $i; $j++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                            <?php for ($j = $i + 1; $j <= 5; $j++): ?>
                                                <i class="far fa-star"></i>
                                            <?php endfor; ?>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="form-text">Select your rating from 1 to 5 stars</div>
                        </div>

                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label">Review (Optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5" 
                                      placeholder="Share your experience with this session..."><?= escape($_POST['comment'] ?? '') ?></textarea>
                            <div class="form-text">Your review will help other learners make informed decisions</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="my_bookings.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-star"></i> Submit Rating
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input .btn-outline-warning {
    font-size: 1.2rem;
    padding: 0.5rem 1rem;
}

.rating-input .btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.rating-input .btn-outline-warning:hover {
    background-color: #ffe69c;
    border-color: #ffc107;
    color: #000;
}
</style>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>
