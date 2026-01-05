<?php
/**
 * Admin Settings Management
 * 
 * Interface for administrators to view and edit application settings
 */

// Include configuration and start session
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/models/Settings.php';

// Require admin role
require_role('admin');

// Handle settings update
$updateMessage = '';
$updateType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_settings' && isset($_POST['settings'])) {
        $userId = $_SESSION['user_id'];
        $result = Settings::updateBatch($_POST['settings'], $userId);
        
        if ($result['success'] > 0) {
            $updateMessage = "Successfully updated {$result['success']} setting(s).";
            $updateType = 'success';
            
            if ($result['failed'] > 0) {
                $updateMessage .= " Failed to update {$result['failed']} setting(s).";
                $updateType = 'warning';
            }
        } else {
            $updateMessage = "Failed to update settings.";
            $updateType = 'error';
        }
    }
    
    if ($_POST['action'] === 'clear_cache') {
        Settings::clearCache();
        $updateMessage = "Settings cache cleared successfully.";
        $updateType = 'success';
    }
}

// Get all editable settings grouped by category
$settingsGrouped = Settings::getGrouped(true); // true = editable only

// Page title
$pageTitle = "Application Settings";

// Include header
include '../app/includes/header.php';
include '../app/includes/navbar.php';
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="fas fa-cog"></i> Application Settings</h1>
                    <p class="text-muted">Configure system-wide application behavior</p>
                </div>
                <div>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="clear_cache">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Clear Cache
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Message -->
            <?php if ($updateMessage): ?>
                <div class="alert alert-<?php echo $updateType === 'success' ? 'success' : ($updateType === 'warning' ? 'warning' : 'danger'); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($updateMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Information Box -->
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> About Settings</h5>
                <p class="mb-2">
                    <strong>Database Settings:</strong> These settings control application behavior and can be edited here.
                    Changes take effect immediately after saving.
                </p>
                <p class="mb-0">
                    <strong>Config File Settings:</strong> Server configuration, database credentials, and sensitive data 
                    are stored in <code>config.php</code> and cannot be edited via this interface.
                </p>
            </div>

            <!-- Settings Form -->
            <form method="POST" id="settingsForm">
                <input type="hidden" name="action" value="update_settings">

                <?php foreach ($settingsGrouped as $groupName => $settings): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-<?php echo getGroupIcon($groupName); ?>"></i>
                                <?php echo ucwords(str_replace('_', ' ', $groupName)); ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($settings as $key => $data): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="setting-item p-3 border rounded">
                                            <label class="form-label fw-bold">
                                                <?php echo formatSettingLabel($key); ?>
                                                <?php if ($data['is_public']): ?>
                                                    <span class="badge bg-info" title="Visible to frontend">Public</span>
                                                <?php endif; ?>
                                            </label>
                                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($data['description']); ?></p>
                                            
                                            <?php if ($data['is_editable']): ?>
                                                <?php renderInput($key, $data); ?>
                                            <?php else: ?>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['value']); ?>" disabled>
                                                <small class="text-muted">This setting is read-only</small>
                                            <?php endif; ?>
                                            
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    Type: <code><?php echo $data['type']; ?></code>
                                                    <?php if ($data['updated_at']): ?>
                                                        | Last updated: <?php echo date('Y-m-d H:i', strtotime($data['updated_at'])); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../app/includes/footer.php'; ?>

<?php
/**
 * Helper function to render appropriate input based on setting type
 */
function renderInput($key, $data) {
    $name = "settings[{$key}]";
    $value = $data['raw_value'];
    $type = $data['type'];
    
    switch ($type) {
        case 'bool':
            ?>
            <select name="<?php echo $name; ?>" class="form-select">
                <option value="true" <?php echo ($data['value'] === true) ? 'selected' : ''; ?>>Enabled (True)</option>
                <option value="false" <?php echo ($data['value'] === false) ? 'selected' : ''; ?>>Disabled (False)</option>
            </select>
            <?php
            break;
            
        case 'int':
            ?>
            <input 
                type="number" 
                name="<?php echo $name; ?>" 
                class="form-control" 
                value="<?php echo htmlspecialchars($value); ?>"
                step="1"
            >
            <?php
            break;
            
        case 'float':
            ?>
            <input 
                type="number" 
                name="<?php echo $name; ?>" 
                class="form-control" 
                value="<?php echo htmlspecialchars($value); ?>"
                step="0.01"
            >
            <?php
            break;
            
        case 'json':
            ?>
            <textarea 
                name="<?php echo $name; ?>" 
                class="form-control font-monospace" 
                rows="4"
            ><?php echo htmlspecialchars($value); ?></textarea>
            <small class="text-muted">Must be valid JSON format</small>
            <?php
            break;
            
        case 'string':
        default:
            ?>
            <input 
                type="text" 
                name="<?php echo $name; ?>" 
                class="form-control" 
                value="<?php echo htmlspecialchars($value); ?>"
            >
            <?php
            break;
    }
}

/**
 * Format setting key into readable label
 */
function formatSettingLabel($key) {
    // Remove group prefix (e.g., "security.session_timeout" -> "session_timeout")
    $parts = explode('.', $key);
    $label = end($parts);
    
    // Convert underscores to spaces and capitalize
    $label = str_replace('_', ' ', $label);
    $label = ucwords($label);
    
    return $label;
}

/**
 * Get icon for settings group
 */
function getGroupIcon($group) {
    $icons = [
        'security' => 'shield-alt',
        'user' => 'users',
        'booking' => 'calendar-check',
        'rating' => 'star',
        'impact' => 'leaf',
        'ui' => 'palette'
    ];
    
    return $icons[$group] ?? 'cog';
}
?>

<style>
.setting-item {
    background-color: #f8f9fa;
    transition: box-shadow 0.2s;
}

.setting-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header h4 {
    font-size: 1.25rem;
}

.badge {
    font-size: 0.7rem;
    vertical-align: middle;
}
</style>

<script>
// Prevent accidental navigation away with unsaved changes
let formChanged = false;

document.getElementById('settingsForm').addEventListener('change', function() {
    formChanged = true;
});

document.getElementById('settingsForm').addEventListener('submit', function() {
    formChanged = false;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
});
</script>
