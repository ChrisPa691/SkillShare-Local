<?php
/**
 * Edit Session Page
 * Form for instructors to edit their existing sessions
 */

session_start();
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Session.php';
require_once __DIR__ . '/../app/controllers/SessionController.php';
require_once __DIR__ . '/../app/includes/helpers.php';
require_once __DIR__ . '/../app/includes/auth_guard.php';

// Require instructor access
require_login();
require_role('instructor');

// Get session ID
$session_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($session_id <= 0) {
    set_flash('error', 'Invalid session ID.');
    redirect('dashboard.php');
}

// Get session details
$session = Session::getSessionById($session_id);
if (!$session) {
    set_flash('error', 'Session not found.');
    redirect('dashboard.php');
}

// Verify ownership
if ($session['instructor_id'] != get_user_id() && !is_admin()) {
    set_flash('error', 'You do not have permission to edit this session.');
    redirect('dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = SessionController::handleUpdate($session_id);
    
    if ($result['success']) {
        set_flash('success', $result['message']);
        redirect('session_view.php?id=' . $session_id);
    } else {
        set_flash('error', $result['message']);
        // Refresh session data to show changes or keep current
        $session = Session::getSessionById($session_id);
    }
}

// Get categories for dropdown
$categories = SessionController::getCategories();

// Page title
$page_title = "Edit Session";
require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/includes/navbar.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-edit text-primary"></i> Edit Session
                </h1>
                <div>
                    <a href="session_view.php?id=<?= $session_id ?>" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-eye"></i> View Session
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>

            <?php display_flash(); ?>

            <!-- Edit Session Form -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="session_edit.php?id=<?= $session_id ?>" enctype="multipart/form-data" id="editSessionForm" class="needs-validation" novalidate>
                        
                        <!-- Basic Information -->
                        <h4 class="mb-3 text-primary"><i class="fas fa-info-circle"></i> Basic Information</h4>
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Session Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= escape($session['title']) ?>" 
                                       required minlength="3" maxlength="100">
                                <div class="form-text">Enter a clear, descriptive title (3-100 characters)</div>
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['category_id'] ?>" 
                                                <?= ($session['category_id'] == $category['category_id']) ? 'selected' : '' ?>>
                                            <?= escape($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" 
                                      required minlength="10"><?= escape($session['description']) ?></textarea>
                            <div class="form-text">Provide a detailed description (minimum 10 characters)</div>
                        </div>

                        <div class="mb-4">
                            <label for="sustainability_description" class="form-label">Sustainability Impact</label>
                            <textarea class="form-control" id="sustainability_description" name="sustainability_description" rows="2"><?= escape($session['sustainability_description'] ?? '') ?></textarea>
                            <div class="form-text">Optional: Explain environmental or social benefits</div>
                        </div>

                        <!-- Session Details -->
                        <h4 class="mb-3 text-primary mt-5"><i class="fas fa-calendar-alt"></i> Session Details</h4>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="event_datetime" class="form-label">Event Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="event_datetime" name="event_datetime" 
                                       value="<?= date('Y-m-d\TH:i', strtotime($session['event_datetime'])) ?>" 
                                       required min="<?= date('Y-m-d\TH:i', strtotime('+1 hour')) ?>">
                                <div class="form-text">Select a future date and time</div>
                            </div>
                            <div class="col-md-3">
                                <label for="duration_minutes" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" 
                                       value="<?= $session['duration_minutes'] ?>" 
                                       required min="15" max="480" step="15">
                                <div class="form-text">15-480 minutes</div>
                            </div>
                            <div class="col-md-3">
                                <label for="total_capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="total_capacity" name="total_capacity" 
                                       value="<?= $session['total_capacity'] ?>" 
                                       required min="<?= $session['total_capacity'] - $session['capacity_remaining'] ?>" max="100">
                                <div class="form-text">Current: <?= $session['capacity_remaining'] ?> remaining</div>
                            </div>
                        </div>

                        <!-- Fee Type -->
                        <h4 class="mb-3 text-primary mt-5"><i class="fas fa-dollar-sign"></i> Fee Information</h4>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fee_type" id="fee_free" value="free" 
                                           <?= ($session['fee_type'] === 'free') ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="fee_free">
                                        <i class="fas fa-gift text-success"></i> Free
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fee_type" id="fee_paid" value="paid"
                                           <?= ($session['fee_type'] === 'paid') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="fee_paid">
                                        <i class="fas fa-money-bill-wave text-primary"></i> Paid
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4" id="fee_amount_container" style="display: none;">
                                <label for="fee_amount" class="form-label">Fee Amount ($) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="fee_amount" name="fee_amount" 
                                       value="<?= $session['fee_amount'] ?>" 
                                       min="0.01" step="0.01">
                                <div class="form-text">Enter amount in dollars</div>
                            </div>
                        </div>

                        <!-- Location Type -->
                        <h4 class="mb-3 text-primary mt-5"><i class="fas fa-map-marker-alt"></i> Location</h4>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Location Type <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="location_type" id="location_online" value="online"
                                           <?= ($session['location_type'] === 'online') ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="location_online">
                                        <i class="fas fa-video text-info"></i> Online
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="location_type" id="location_inperson" value="in-person"
                                           <?= ($session['location_type'] === 'in-person') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="location_inperson">
                                        <i class="fas fa-building text-warning"></i> In-Person
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Online Link -->
                            <div class="col-md-8" id="online_link_container" style="display: none;">
                                <label for="online_link" class="form-label">Online Meeting Link <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="online_link" name="online_link" 
                                       value="<?= escape($session['online_link'] ?? '') ?>">
                                <div class="form-text">Provide Zoom, Google Meet, or other video conferencing link</div>
                            </div>
                        </div>

                        <!-- In-Person Location -->
                        <div id="inperson_location_container" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?= escape($session['city'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" 
                                           value="<?= escape($session['address'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Photo Upload -->
                        <h4 class="mb-3 text-primary mt-5"><i class="fas fa-image"></i> Session Photo</h4>
                        <?php if (!empty($session['photo_url'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Photo:</label><br>
                                <img src="<?= escape($session['photo_url']) ?>" alt="Session photo" class="img-thumbnail" style="max-width: 300px;">
                            </div>
                        <?php endif; ?>
                        <div class="mb-4">
                            <label for="session_photo" class="form-label">Upload New Photo (Optional)</label>
                            <input type="file" class="form-control" id="session_photo" name="session_photo" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text">Optional: Upload a new image to replace the current one (JPEG, PNG, WebP - max 5MB)</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                            <a href="session_view.php?id=<?= $session_id ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/sessions.js"></script>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>
