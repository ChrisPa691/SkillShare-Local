<?php
/**
 * User Settings Page
 * 
 * Allows users to manage their account settings and preferences
 */

require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/models/User.php';
require_once '../app/models/UserSettings.php';

// Require authentication
require_login();

$userId = $_SESSION['user_id'];
$user = User::getById($userId);

// Load user preferences from UserSettings
$userSettings = UserSettings::get($userId);
if (!$userSettings) {
    UserSettings::create($userId);
    $userSettings = UserSettings::get($userId);
}

$userTheme = $userSettings['theme'] ?? 'light';
$emailNotifications = $userSettings['notify_email'] ?? true;
$bookingReminders = $userSettings['notify_inapp'] ?? true;
$marketingEmails = $userSettings['notify_push'] ?? false;
$userLanguage = $userSettings['language'] ?? 'en';
$userTimezone = $userSettings['timezone'] ?? 'UTC';
$userCurrency = $userSettings['currency'] ?? 'GBP';
$fontSize = $userSettings['font_size'] ?? 16;
$lineHeight = $userSettings['line_height'] ?? 1.50;
$contrastMode = $userSettings['contrast_mode'] ?? 'normal';

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update Profile Information
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $updateData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'city' => trim($_POST['city']),
            'bio' => trim($_POST['bio'])
        ];
        
        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileType = $_FILES['profile_photo']['type'];
            $fileSize = $_FILES['profile_photo']['size'];
            
            if (!in_array($fileType, $allowedTypes)) {
                $message = "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.";
                $messageType = 'error';
            } elseif ($fileSize > $maxSize) {
                $message = "File too large. Maximum size is 5MB.";
                $messageType = 'error';
            } else {
                // Create uploads directory if it doesn't exist
                $uploadDir = 'assets/uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate unique filename
                $extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
                    // Delete old avatar if exists (check both relative and absolute paths)
                    if (!empty($user['avatar_path'])) {
                        $oldAvatarPath = $user['avatar_path'];
                        // Try deleting with both the stored path and checking if it's in the current directory
                        if (file_exists($oldAvatarPath)) {
                            @unlink($oldAvatarPath);
                        } elseif (file_exists(__DIR__ . '/' . $oldAvatarPath)) {
                            @unlink(__DIR__ . '/' . $oldAvatarPath);
                        }
                    }
                    
                    $updateData['avatar_path'] = $uploadPath;
                } else {
                    $message = "Failed to upload profile photo.";
                    $messageType = 'error';
                }
            }
        }
        
        // Validate email format
        if (!isset($messageType) || $messageType !== 'error') {
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
        try {
            $updateData = [
                'notify_email' => isset($_POST['email_notifications']) ? 1 : 0,
                'notify_inapp' => isset($_POST['booking_reminders']) ? 1 : 0,
                'notify_push' => isset($_POST['marketing_emails']) ? 1 : 0,
                'theme' => $_POST['theme_mode'] ?? 'light',
                'language' => $_POST['language'] ?? 'en',
                'timezone' => $_POST['timezone'] ?? 'UTC',
                'currency' => $_POST['currency'] ?? 'GBP',
                'font_size' => isset($_POST['font_size']) ? (int)$_POST['font_size'] : 16,
                'contrast_mode' => $_POST['contrast_mode'] ?? 'normal'
            ];
            
            UserSettings::update($userId, $updateData);
            
            // Reload preferences
            $userSettings = UserSettings::get($userId);
            $userTheme = $userSettings['theme'] ?? 'light';
            $emailNotifications = $userSettings['notify_email'] ?? true;
            $bookingReminders = $userSettings['notify_inapp'] ?? true;
            $marketingEmails = $userSettings['notify_push'] ?? false;
            $userLanguage = $userSettings['language'] ?? 'en';
            $userTimezone = $userSettings['timezone'] ?? 'UTC';
            $userCurrency = $userSettings['currency'] ?? 'GBP';
            $fontSize = $userSettings['font_size'] ?? 16;
            $lineHeight = $userSettings['line_height'] ?? 1.50;
            $contrastMode = $userSettings['contrast_mode'] ?? 'normal';
            
            // Return JSON for AJAX requests
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Preferences updated successfully',
                    'preferences' => $userSettings
                ]);
                exit;
            }
            
            $message = "Preferences updated successfully.";
            $messageType = 'success';
        } catch (Exception $e) {
            // Return JSON for AJAX requests
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update preferences: ' . $e->getMessage()
                ]);
                exit;
            }
            
            $message = "Failed to update preferences: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

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
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_profile">
                                
                                <!-- Profile Photo -->
                                <div class="mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <?php if (!empty($user['avatar_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['avatar_path']); ?>" 
                                                 alt="Profile Photo" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white me-3" 
                                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <input 
                                                type="file" 
                                                name="profile_photo" 
                                                class="form-control" 
                                                accept="image/jpeg,image/png,image/gif,image/webp"
                                            >
                                            <small class="text-muted">JPG, PNG, GIF or WebP. Max 5MB.</small>
                                        </div>
                                    </div>
                                </div>
                                
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
                                    <label class="form-label">Bio</label>
                                    <textarea 
                                        name="bio" 
                                        class="form-control" 
                                        rows="4"
                                        maxlength="500"
                                        placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    <small class="text-muted">Maximum 500 characters. This will be visible on your profile.</small>
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
                            <form method="POST" id="preferencesForm">
                                <input type="hidden" name="action" value="update_preferences">
                                
                                <div class="form-check form-switch mb-3">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="email_notifications" 
                                        id="emailNotifications"
                                        <?php echo $emailNotifications ? 'checked' : ''; ?>
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
                                        <?php echo $bookingReminders ? 'checked' : ''; ?>
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
                                        <?php echo $marketingEmails ? 'checked' : ''; ?>
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
                                    <label class="form-label">Language</label>
                                    <select class="form-select" name="language" id="language">
                                        <option value="en" <?php echo $userLanguage === 'en' ? 'selected' : ''; ?>>English</option>
                                        <option value="el" <?php echo $userLanguage === 'el' ? 'selected' : ''; ?>>Ελληνικά (Greek)</option>
                                        <option value="es" <?php echo $userLanguage === 'es' ? 'selected' : ''; ?>>Español</option>
                                        <option value="fr" <?php echo $userLanguage === 'fr' ? 'selected' : ''; ?>>Français</option>
                                        <option value="de" <?php echo $userLanguage === 'de' ? 'selected' : ''; ?>>Deutsch</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred language</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Timezone</label>
                                    <select class="form-select" name="timezone" id="timezone">
                                        <option value="Europe/London" <?php echo $userTimezone === 'Europe/London' ? 'selected' : ''; ?>>London (GMT)</option>
                                        <option value="Europe/Athens" <?php echo $userTimezone === 'Europe/Athens' ? 'selected' : ''; ?>>Athens (EET)</option>
                                        <option value="Asia/Nicosia" <?php echo $userTimezone === 'Asia/Nicosia' ? 'selected' : ''; ?>>Nicosia (EET)</option>
                                        <option value="America/New_York" <?php echo $userTimezone === 'America/New_York' ? 'selected' : ''; ?>>New York (EST)</option>
                                        <option value="America/Los_Angeles" <?php echo $userTimezone === 'America/Los_Angeles' ? 'selected' : ''; ?>>Los Angeles (PST)</option>
                                    </select>
                                    <small class="text-muted">Choose your timezone</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Preferred Currency</label>
                                    <select class="form-select" name="currency" id="preferredCurrency">
                                        <option value="GBP" <?php echo $userCurrency === 'GBP' ? 'selected' : ''; ?>>British Pound (£)</option>
                                        <option value="USD" <?php echo $userCurrency === 'USD' ? 'selected' : ''; ?>>US Dollar ($)</option>
                                        <option value="EUR" <?php echo $userCurrency === 'EUR' ? 'selected' : ''; ?>>Euro (€)</option>
                                        <option value="CAD" <?php echo $userCurrency === 'CAD' ? 'selected' : ''; ?>>Canadian Dollar (CA$)</option>
                                        <option value="AUD" <?php echo $userCurrency === 'AUD' ? 'selected' : ''; ?>>Australian Dollar (AU$)</option>
                                        <option value="JPY" <?php echo $userCurrency === 'JPY' ? 'selected' : ''; ?>>Japanese Yen (¥)</option>
                                        <option value="INR" <?php echo $userCurrency === 'INR' ? 'selected' : ''; ?>>Indian Rupee (₹)</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred currency for displaying prices</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Theme</label>
                                    <select class="form-select" name="theme_mode" id="themeMode">
                                        <option value="light" <?php echo $userTheme === 'light' ? 'selected' : ''; ?>>Light Mode</option>
                                        <option value="dark" <?php echo $userTheme === 'dark' ? 'selected' : ''; ?>>Dark Mode</option>
                                        <option value="auto" <?php echo $userTheme === 'auto' ? 'selected' : ''; ?>>Auto (System Preference)</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred color theme</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Font Size</label>
                                    <select class="form-select" name="font_size">
                                        <option value="12" <?php echo $fontSize == 12 ? 'selected' : ''; ?>>12px (Small)</option>
                                        <option value="14" <?php echo $fontSize == 14 ? 'selected' : ''; ?>>14px</option>
                                        <option value="16" <?php echo $fontSize == 16 ? 'selected' : ''; ?>>16px (Normal)</option>
                                        <option value="18" <?php echo $fontSize == 18 ? 'selected' : ''; ?>>18px</option>
                                        <option value="20" <?php echo $fontSize == 20 ? 'selected' : ''; ?>>20px (Large)</option>
                                        <option value="24" <?php echo $fontSize == 24 ? 'selected' : ''; ?>>24px (Extra Large)</option>
                                    </select>
                                    <small class="text-muted">Adjust text size for better readability</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contrast Mode</label>
                                    <select class="form-select" name="contrast_mode">
                                        <option value="normal" <?php echo $contrastMode === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                        <option value="high" <?php echo $contrastMode === 'high' ? 'selected' : ''; ?>>High Contrast</option>
                                    </select>
                                    <small class="text-muted">Enhance visibility with high contrast mode</small>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary" id="savePreferencesBtn">
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
                                <div class="col-sm-4 fw-bold">Registration:</div>
                                <div class="col-sm-8">Open</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Platform Fee:</div>
                                <div class="col-sm-8">10%</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Max Session Duration:</div>
                                <div class="col-sm-8">8 hours</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Sustainability Tracking:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-success">Enabled</span>
                                    <br>
                                    <small class="text-muted">
                                        Display unit: kg CO₂e<br>
                                        Avg commute distance: 10 km
                                    </small>
                                </div>
                            </div>

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

<script src="assets/js/settings.js"></script>
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
if (const hash = window.location.hash;
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
} // No need for duplicate initialization here
});
</script>

<style>
.list-group-item {
    transition: all 0.2s;
}

.list-group-item:hover {
    background-color: var(--bg-hover);
}

.list-group-item.active {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

.card {
    border-radius: 8px;
    box-shadow: var(--shadow-sm);
}

.card-header {
    background-color: var(--bg-secondary);
    border-bottom: 2px solid var(--border-color);
}

.form-check-input:checked {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

.tab-content {
    min-height: 400px;
}
</style>
