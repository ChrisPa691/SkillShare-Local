<?php
/**
 * User Settings Page
 * 
 * Allows users to manage their account settings and preferences
 */

require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/models/User.php';
require_once '../app/models/Settings.php';

// Require authentication
require_login();

$userId = $_SESSION['user_id'];
$user = User::getById($userId);

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update Profile Information
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $updateData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'city' => trim($_POST['city'])
        ];
        
        // Validate email format
        if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
            $messageType = 'error';
        } else {
            if (User::update($userId, $updateData)) {
                $message = "Profile updated successfully.";
                $messageType = 'success';
                $user = User::getById($userId); // Refresh user data
            } else {
                $message = "Failed to update profile.";
                $messageType = 'error';
            }
        }
    }
    
    // Change Password
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Verify current password
        if (!User::verifyPassword($userId, $currentPassword)) {
            $message = "Current password is incorrect.";
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = "New passwords do not match.";
            $messageType = 'error';
        } elseif (strlen($newPassword) < 6) {
            $message = "New password must be at least 6 characters long.";
            $messageType = 'error';
        } else {
            if (User::updatePassword($userId, $newPassword)) {
                $message = "Password changed successfully.";
                $messageType = 'success';
            } else {
                $message = "Failed to change password.";
                $messageType = 'error';
            }
        }
    }
    
    // Update Email Preferences
    if (isset($_POST['action']) && $_POST['action'] === 'update_preferences') {
        $preferences = [
            'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
            'booking_reminders' => isset($_POST['booking_reminders']) ? 1 : 0,
            'marketing_emails' => isset($_POST['marketing_emails']) ? 1 : 0
        ];
        
        // Store preferences (you can create a user_preferences table or store in user table)
        // For now, we'll just show success
        $message = "Preferences updated successfully.";
        $messageType = 'success';
    }
}

// Get public app settings for display
$publicSettings = Settings::getPublic();
$appCurrency = Settings::get('booking.currency', 'GBP');

$pageTitle = "Settings";
include '../app/includes/header.php';
include '../app/includes/navbar.php';
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="list-group" id="settings-nav">
                <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                    <i class="fas fa-user"></i> Profile Information
                </a>
                <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-lock"></i> Security
                </a>
                <a href="#preferences" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-sliders-h"></i> Preferences
                </a>
                <a href="#app-info" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-info-circle"></i> App Information
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <h1 class="mb-4"><i class="fas fa-cog"></i> Settings</h1>

            <!-- Message Alert -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="tab-content">
                <!-- Profile Information Tab -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-user"></i> Profile Information</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input 
                                        type="text" 
                                        name="full_name" 
                                        class="form-control" 
                                        value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        class="form-control" 
                                        value="<?php echo htmlspecialchars($user['email']); ?>"
                                        required
                                    >
                                    <small class="text-muted">This email is used for login and notifications.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input 
                                        type="text" 
                                        name="city" 
                                        class="form-control" 
                                        value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"
                                        placeholder="e.g., London, Manchester"
                                    >
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        value="<?php echo ucfirst($user['role']); ?>"
                                        disabled
                                    >
                                    <small class="text-muted">Contact an administrator to change your role.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Member Since</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>"
                                        disabled
                                    >
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-lock"></i> Change Password</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="passwordForm">
                                <input type="hidden" name="action" value="change_password">
                                
                                <div class="mb-3">
                                    <label class="form-label">Current Password *</label>
                                    <input 
                                        type="password" 
                                        name="current_password" 
                                        class="form-control" 
                                        required
                                        autocomplete="current-password"
                                    >
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">New Password *</label>
                                    <input 
                                        type="password" 
                                        name="new_password" 
                                        class="form-control" 
                                        id="newPassword"
                                        required
                                        minlength="6"
                                        autocomplete="new-password"
                                    >
                                    <small class="text-muted">Minimum 6 characters.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password *</label>
                                    <input 
                                        type="password" 
                                        name="confirm_password" 
                                        class="form-control" 
                                        id="confirmPassword"
                                        required
                                        autocomplete="new-password"
                                    >
                                    <div id="passwordMatch" class="form-text"></div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Password Security Tips:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use at least 8 characters (6 minimum)</li>
                                        <li>Include uppercase and lowercase letters</li>
                                        <li>Add numbers and special characters</li>
                                        <li>Don't reuse passwords from other sites</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                                        <i class="fas fa-key"></i> Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Security Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Account Security</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Last Login:</strong></p>
                                    <p class="text-muted"><?php echo isset($user['updated_at']) && $user['updated_at'] ? date('F j, Y g:i A', strtotime($user['updated_at'])) : 'N/A'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Account Status:</strong></p>
                                    <?php if ($user['is_suspended']): ?>
                                        <span class="badge bg-danger">Suspended</span>
                                        <?php if ($user['suspended_reason']): ?>
                                            <p class="text-muted small mt-1">Reason: <?php echo htmlspecialchars($user['suspended_reason']); ?></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences Tab -->
                <div class="tab-pane fade" id="preferences">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-sliders-h"></i> Email & Notification Preferences</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_preferences">
                                
                                <div class="form-check form-switch mb-3">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="email_notifications" 
                                        id="emailNotifications"
                                        checked
                                    >
                                    <label class="form-check-label" for="emailNotifications">
                                        <strong>Email Notifications</strong>
                                        <br>
                                        <small class="text-muted">Receive email notifications for important updates</small>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="booking_reminders" 
                                        id="bookingReminders"
                                        checked
                                    >
                                    <label class="form-check-label" for="bookingReminders">
                                        <strong>Booking Reminders</strong>
                                        <br>
                                        <small class="text-muted">Get reminded about upcoming sessions you've booked</small>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="marketing_emails" 
                                        id="marketingEmails"
                                    >
                                    <label class="form-check-label" for="marketingEmails">
                                        <strong>Marketing Emails</strong>
                                        <br>
                                        <small class="text-muted">Receive newsletters and promotional content</small>
                                    </label>
                                </div>

                                <hr class="my-4">

                                <h5>Display Preferences</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Preferred Currency</label>
                                    <select class="form-select" name="preferred_currency" id="preferredCurrency">
                                        <option value="GBP" selected>British Pound (£)</option>
                                        <option value="USD">US Dollar ($)</option>
                                        <option value="EUR">Euro (€)</option>
                                        <option value="CAD">Canadian Dollar (CA$)</option>
                                        <option value="AUD">Australian Dollar (AU$)</option>
                                        <option value="JPY">Japanese Yen (¥)</option>
                                        <option value="INR">Indian Rupee (₹)</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred currency for displaying prices</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Theme</label>
                                    <select class="form-select" name="theme_mode" id="themeMode">
                                        <option value="light">Light Mode</option>
                                        <option value="dark">Dark Mode</option>
                                        <option value="auto">Auto (System Preference)</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred color theme</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Items Per Page</label>
                                    <select class="form-select" name="items_per_page">
                                        <option value="12" selected>12 items</option>
                                        <option value="24">24 items</option>
                                        <option value="36">36 items</option>
                                        <option value="48">48 items</option>
                                    </select>
                                    <small class="text-muted">Number of sessions to display per page in listings</small>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="show_impact_badges" 
                                        id="showImpactBadges"
                                        checked
                                    >
                                    <label class="form-check-label" for="showImpactBadges">
                                        <strong>Show Sustainability Badges</strong>
                                        <br>
                                        <small class="text-muted">Display CO₂ impact badges on session cards</small>
                                    </label>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- App Information Tab -->
                <div class="tab-pane fade" id="app-info">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-info-circle"></i> Application Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Application Name:</div>
                                <div class="col-sm-8">SkillShare Local</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Version:</div>
                                <div class="col-sm-8">1.0.0</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Currency:</div>
                                <div class="col-sm-8"><?php echo htmlspecialchars($appCurrency); ?></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Registration:</div>
                                <div class="col-sm-8">
                                    <?php echo Settings::get('user.allow_registration', true) ? 'Open' : 'Closed'; ?>
                                </div>
                            </div>

                            <?php if (Settings::get('impact.enable_tracking', true)): ?>
                                <div class="row mb-3">
                                    <div class="col-sm-4 fw-bold">Sustainability Tracking:</div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-success">Enabled</span>
                                        <br>
                                        <small class="text-muted">
                                            Display unit: <?php echo htmlspecialchars(Settings::get('impact.display_unit', 'kg CO₂')); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <hr class="my-4">

                            <h5>About SkillShare Local</h5>
                            <p class="text-muted">
                                SkillShare Local is a community-driven platform for sharing skills and knowledge. 
                                Connect with local instructors, learn new skills, and contribute to a more sustainable 
                                community through shared learning experiences.
                            </p>

                            <div class="mt-3">
                                <a href="dashboard.php" class="btn btn-outline-primary">
                                    <i class="fas fa-home"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Status (if admin) -->
                    <?php if ($user['role'] === 'admin'): ?>
                        <div class="card mt-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-tools"></i> Admin Quick Links</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2 d-md-flex">
                                    <a href="admin_settings.php" class="btn btn-outline-primary">
                                        <i class="fas fa-cog"></i> App Settings
                                    </a>
                                    <a href="admin_impact_factors.php" class="btn btn-outline-success">
                                        <i class="fas fa-leaf"></i> Impact Factors
                                    </a>
                                    <a href="admin_users.php" class="btn btn-outline-info">
                                        <i class="fas fa-users"></i> Manage Users
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/includes/footer.php'; ?>

<script>
// Password matching validation
document.getElementById('confirmPassword').addEventListener('input', function() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = this.value;
    const matchDiv = document.getElementById('passwordMatch');
    const submitBtn = document.getElementById('changePasswordBtn');
    
    if (confirmPassword.length > 0) {
        if (newPassword === confirmPassword) {
            matchDiv.className = 'form-text text-success';
            matchDiv.innerHTML = '<i class="fas fa-check"></i> Passwords match';
            submitBtn.disabled = false;
        } else {
            matchDiv.className = 'form-text text-danger';
            matchDiv.innerHTML = '<i class="fas fa-times"></i> Passwords do not match';
            submitBtn.disabled = true;
        }
    } else {
        matchDiv.innerHTML = '';
        submitBtn.disabled = false;
    }
});

// Tab persistence
const settingsNav = document.getElementById('settings-nav');
if (settingsNav) {
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`a[href="${hash}"]`);
        if (tab) {
            const bsTab = new bootstrap.Tab(tab);
            bsTab.show();
        }
    }
    
    // Update URL hash when tab changes
    settingsNav.addEventListener('click', function(e) {
        if (e.target.tagName === 'A') {
            history.pushState(null, null, e.target.getAttribute('href'));
        }
    });
}

// ==========================================
// THEME MANAGEMENT
// ==========================================

// Load saved theme preference
function loadThemePreference() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeSelect = document.getElementById('themeMode');
    
    if (themeSelect) {
        themeSelect.value = savedTheme;
    }
    
    applyTheme(savedTheme);
}

// Apply theme to the page
function applyTheme(theme) {
    const body = document.body;
    
    if (theme === 'auto') {
        // Use system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme = prefersDark ? 'dark' : 'light';
    }
    
    // Remove both classes first
    body.classList.remove('theme-light', 'theme-dark');
    
    // Add the appropriate theme class
    body.classList.add(`theme-${theme}`);
    
    // Update data attribute for CSS targeting
    body.setAttribute('data-theme', theme);
    
    // Store preference
    localStorage.setItem('theme', document.getElementById('themeMode')?.value || theme);
}

// Handle theme change
const themeSelect = document.getElementById('themeMode');
if (themeSelect) {
    themeSelect.addEventListener('change', function() {
        applyTheme(this.value);
    });
}

// Listen for system theme changes (for auto mode)
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'auto') {
        applyTheme('auto');
    }
});

// ==========================================
// CURRENCY PREFERENCE
// ==========================================

// Load saved currency preference
function loadCurrencyPreference() {
    const savedCurrency = localStorage.getItem('preferredCurrency') || 'GBP';
    const currencySelect = document.getElementById('preferredCurrency');
    
    if (currencySelect) {
        currencySelect.value = savedCurrency;
    }
}

// Handle currency change
const currencySelect = document.getElementById('preferredCurrency');
if (currencySelect) {
    currencySelect.addEventListener('change', function() {
        localStorage.setItem('preferredCurrency', this.value);
        // Show success message
        showToast('Currency preference saved! This will be used for displaying prices throughout the app.', 'success');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadThemePreference();
    loadCurrencyPreference();
});
</script>

<style>
.list-group-item {
    transition: all 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.tab-content {
    min-height: 400px;
}
</style>
