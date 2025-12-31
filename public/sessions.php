<?php
/**
 * Sessions Browse Page
 * Display all available skill-sharing sessions with search and filters
 */

session_start();

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/includes/helpers.php';

// Get all categories for filter dropdown
$categories = db_select('Categories', [], 'ORDER BY name ASC');

// Get unique cities for location filter
$cities_result = db_query("SELECT DISTINCT city FROM skill_sessions WHERE city IS NOT NULL ORDER BY city ASC");
$cities = $cities_result ? $cities_result : [];

// Build filters array from $_GET parameters
$filters = [];
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
    $filters['category_id'] = intval($_GET['category_id']);
}
if (isset($_GET['location_type']) && $_GET['location_type'] !== '') {
    $filters['location_type'] = $_GET['location_type'];
}
if (isset($_GET['fee_type']) && $_GET['fee_type'] !== '') {
    $filters['fee_type'] = $_GET['fee_type'];
}
if (isset($_GET['city']) && $_GET['city'] !== '') {
    $filters['city'] = $_GET['city'];
}
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $filters['search'] = $_GET['search'];
}

// Get sessions with filters
$sessions = Session::getAllSessions($filters);
if ($sessions === false || empty($sessions)) {
    $sessions = [];
    $no_results = true;
} else {
    $no_results = false;
}

// Store current filter values for form
$current_category = $_GET['category_id'] ?? '';
$current_location = $_GET['location_type'] ?? '';
$current_fee = $_GET['fee_type'] ?? '';
$current_city = $_GET['city'] ?? '';
$current_search = $_GET['search'] ?? '';

// Page metadata
$page_title = "Browse Skill Sessions";
$page = 'sessions';

require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/includes/navbar.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-search me-2"></i>Browse Skill Sessions
            </h1>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="sessions.php" id="filterForm">
                        <div class="row g-3">
                            
                            <!-- Search Box -->
                            <div class="col-md-12">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by title or description..." value="<?= escape($current_search) ?>">
                            </div>

                            <!-- Category Filter -->
                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php if ($categories): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['category_id'] ?>" <?= $current_category == $category['category_id'] ? 'selected' : '' ?>>
                                                <?= escape($category['category_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Location Type Filter -->
                            <div class="col-md-3">
                                <label for="location_type" class="form-label">Location Type</label>
                                <select name="location_type" id="location_type" class="form-select">
                                    <option value="">All Locations</option>
                                    <option value="online" <?= $current_location === 'online' ? 'selected' : '' ?>>Online</option>
                                    <option value="in-person" <?= $current_location === 'in-person' ? 'selected' : '' ?>>In-Person</option>
                                </select>
                            </div>

                            <!-- Fee Type Filter -->
                            <div class="col-md-3">
                                <label for="fee_type" class="form-label">Fee Type</label>
                                <select name="fee_type" id="fee_type" class="form-select">
                                    <option value="">All</option>
                                    <option value="free" <?= $current_fee === 'free' ? 'selected' : '' ?>>Free</option>
                                    <option value="paid" <?= $current_fee === 'paid' ? 'selected' : '' ?>>Paid</option>
                                </select>
                            </div>

                            <!-- City Filter -->
                            <div class="col-md-3">
                                <label for="city" class="form-label">City</label>
                                <select name="city" id="city" class="form-select">
                                    <option value="">All Cities</option>
                                    <?php if ($cities): ?>
                                        <?php foreach ($cities as $city_row): ?>
                                            <option value="<?= escape($city_row['city']) ?>" <?= $current_city === $city_row['city'] ? 'selected' : '' ?>>
                                                <?= escape($city_row['city']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Apply Filters
                                </button>
                                <a href="sessions.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Count -->
    <div class="row mb-3">
        <div class="col-12">
            <p class="text-muted">Found <?= count($sessions) ?> session(s)</p>
        </div>
    </div>

    <!-- Sessions Grid -->
    <div class="row">
        <?php if (empty($sessions)): ?>
            <!-- No Results Message -->
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No sessions found matching your criteria. Try adjusting your filters.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($sessions as $session): ?>
                <!-- Session Card -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        
                        <!-- Session Image -->
                        <?php if (!empty($session['photo_url'])): ?>
                            <img src="<?= escape($session['photo_url']) ?>" class="card-img-top" alt="<?= escape($session['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-chalkboard-teacher fa-4x text-white-50"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            
                            <!-- Session Title -->
                            <h5 class="card-title">
                                <a href="session_view.php?id=<?= $session['session_id'] ?>" class="text-decoration-none">
                                    <?= escape($session['title']) ?>
                                </a>
                            </h5>
                            
                            <!-- Category Badge -->
                            <span class="badge bg-secondary mb-2"><?= escape($session['category_name']) ?></span>
                            
                            <!-- Description (truncated) -->
                            <p class="card-text text-muted small">
                                <?= escape(substr($session['description'], 0, 100)) . (strlen($session['description']) > 100 ? '...' : '') ?>
                            </p>
                            
                            <!-- Instructor Name -->
                            <p class="text-muted small mb-1">
                                <i class="fas fa-user me-1"></i><?= escape($session['instructor_name']) ?>
                            </p>
                            
                            <!-- Date and Time -->
                            <p class="text-muted small mb-1">
                                <i class="fas fa-calendar me-1"></i><?= date('M d, Y - h:i A', strtotime($session['event_datetime'])) ?>
                            </p>
                            
                            <!-- Duration -->
                            <p class="text-muted small mb-1">
                                <i class="fas fa-clock me-1"></i><?= $session['duration_minutes'] ?> minutes
                            </p>
                            
                            <!-- Location -->
                            <?php if ($session['location_type'] === 'online'): ?>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-video me-1"></i>Online Session
                                </p>
                            <?php else: ?>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i><?= escape($session['city']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Fee and Capacity -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <?php if ($session['fee_type'] === 'free'): ?>
                                    <span class="badge bg-success">Free</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">$<?= number_format($session['fee_amount'], 2) ?></span>
                                <?php endif; ?>
                                
                                <small class="text-muted">
                                    <?= $session['capacity_remaining'] ?> / <?= $session['total_capacity'] ?> seats
                                </small>
                            </div>
                            
                            <!-- Rating -->
                            <?php if (!empty($session['average_rating']) && $session['average_rating'] > 0): ?>
                                <div class="text-warning small mb-2">
                                    <?php 
                                    $rating = round($session['average_rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '★' : '☆';
                                    }
                                    ?>
                                    <span class="text-muted"><?= number_format($session['average_rating'], 1) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- View Details Button -->
                            <div class="mt-auto">
                                <a href="session_view.php?id=<?= $session['session_id'] ?>" class="btn btn-primary btn-sm w-100">
                                    View Details
                                </a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
</div>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>
