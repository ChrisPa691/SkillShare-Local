<?php
/**
 * Admin Impact Factors Management
 * 
 * Interface for administrators to manage sustainability impact factors
 */

// Include configuration and start session
require_once '../app/config/database.php';
require_once '../app/includes/auth_guard.php';
require_once '../app/models/ImpactFactor.php';

// Require admin role
require_role('admin');

// Handle actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    // Create new factor
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        if (ImpactFactor::categoryExists($_POST['skill_category'])) {
            $message = "A factor for '{$_POST['skill_category']}' already exists.";
            $messageType = 'error';
        } else {
            $data = [
                'skill_category' => $_POST['skill_category'],
                'co2_saved_per_participant_kg' => $_POST['co2_saved'],
                'source_note' => $_POST['source_note'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'updated_by' => $userId
            ];
            
            if (ImpactFactor::create($data)) {
                $message = "Impact factor created successfully.";
                $messageType = 'success';
            } else {
                $message = "Failed to create impact factor.";
                $messageType = 'error';
            }
        }
    }
    
    // Update factor
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['factor_id'];
        $data = [
            'skill_category' => $_POST['skill_category'],
            'co2_saved_per_participant_kg' => $_POST['co2_saved'],
            'source_note' => $_POST['source_note'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        if (ImpactFactor::update($id, $data, $userId)) {
            $message = "Impact factor updated successfully.";
            $messageType = 'success';
        } else {
            $message = "Failed to update impact factor.";
            $messageType = 'error';
        }
    }
    
    // Toggle active status
    if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
        $id = $_POST['factor_id'];
        if (ImpactFactor::toggleActive($id, $userId)) {
            $message = "Impact factor status toggled.";
            $messageType = 'success';
        } else {
            $message = "Failed to toggle status.";
            $messageType = 'error';
        }
    }
    
    // Delete factor
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['factor_id'];
        if (ImpactFactor::delete($id)) {
            $message = "Impact factor deleted successfully.";
            $messageType = 'success';
        } else {
            $message = "Failed to delete impact factor.";
            $messageType = 'error';
        }
    }
}

// Get all impact factors
$factors = ImpactFactor::getAll(true); // Include inactive

// Page title
$pageTitle = "Impact Factors Management";

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
                    <h1><i class="fas fa-leaf"></i> Sustainability Impact Factors</h1>
                    <p class="text-muted">Manage CO₂ savings calculations per skill category</p>
                </div>
                <div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fas fa-plus"></i> Add New Factor
                    </button>
                </div>
            </div>

            <!-- Message -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Information -->
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> About Impact Factors</h5>
                <p class="mb-0">
                    Impact factors define how much CO₂ (in kg) is saved per participant in each skill category.
                    These values are used to calculate the total sustainability impact on the Impact Dashboard.
                </p>
            </div>

            <!-- Impact Factors Table -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-table"></i> Impact Factors</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Skill Category</th>
                                    <th>CO₂ Saved (kg/participant)</th>
                                    <th>Source Note</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($factors)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No impact factors found. Add your first factor above.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($factors as $factor): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($factor['skill_category']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?php echo number_format($factor['co2_saved_per_participant_kg'], 2); ?> kg</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo $factor['source_note'] ? htmlspecialchars(substr($factor['source_note'], 0, 60)) . '...' : '-'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($factor['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo date('Y-m-d H:i', strtotime($factor['updated_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button 
                                                        class="btn btn-outline-primary" 
                                                        onclick="editFactor(<?php echo htmlspecialchars(json_encode($factor)); ?>)"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle">
                                                        <input type="hidden" name="factor_id" value="<?php echo $factor['id']; ?>">
                                                        <button 
                                                            type="submit" 
                                                            class="btn btn-outline-warning"
                                                            title="<?php echo $factor['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $factor['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <button 
                                                        class="btn btn-outline-danger" 
                                                        onclick="deleteFactor(<?php echo $factor['id']; ?>, '<?php echo htmlspecialchars($factor['skill_category']); ?>')"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Impact Factor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Skill Category *</label>
                        <input type="text" name="skill_category" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CO₂ Saved (kg per participant) *</label>
                        <input type="number" name="co2_saved" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Source Note</label>
                        <textarea name="source_note" class="form-control" rows="3"></textarea>
                        <small class="text-muted">Optional: Methodology or citation</small>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="createActive" checked>
                        <label class="form-check-label" for="createActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Factor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="factor_id" id="editId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Impact Factor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Skill Category *</label>
                        <input type="text" name="skill_category" id="editCategory" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CO₂ Saved (kg per participant) *</label>
                        <input type="number" name="co2_saved" id="editCO2" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Source Note</label>
                        <textarea name="source_note" id="editSource" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="editActive">
                        <label class="form-check-label" for="editActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Factor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form method="POST" id="deleteForm" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="factor_id" id="deleteId">
</form>

<?php include '../app/includes/footer.php'; ?>

<script>
function editFactor(factor) {
    document.getElementById('editId').value = factor.id;
    document.getElementById('editCategory').value = factor.skill_category;
    document.getElementById('editCO2').value = factor.co2_saved_per_participant_kg;
    document.getElementById('editSource').value = factor.source_note || '';
    document.getElementById('editActive').checked = factor.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteFactor(id, category) {
    if (confirm(`Are you sure you want to delete the impact factor for "${category}"?\n\nThis action cannot be undone.`)) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
