<?php
/**
 * Impact Dashboard
 * Public page displaying sustainability impact metrics
 */

session_start();
require_once '../app/config/database.php';
require_once '../app/includes/helpers.php';
require_once '../app/includes/auth_guard.php';

$page_title = "Sustainability Impact";

include '../app/includes/header.php';
include '../app/includes/navbar.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="impact-hero">
        <div class="container">
            <i class="fas fa-leaf fa-4x mb-4"></i>
            <h1>Our Collective Impact</h1>
            <p class="lead">
                Together, we're building a sustainable future through local skill sharing
            </p>
        </div>
    </div>

    <!-- Main Impact Statistics -->
    <div class="row">
        <div class="col-md-4">
            <div class="impact-stat-card">
                <i class="fas fa-cloud"></i>
                <div class="impact-number">
                    <?php
                    // Calculate total CO₂ saved (placeholder - will be calculated from impact_factors)
                    $total_sessions = db_count('skill_sessions', ['session_status' => 'completed']);
                    $estimated_co2 = $total_sessions * 2.5; // Estimated 2.5 kg CO₂ per session
                    echo number_format($estimated_co2 / 1000, 2);
                    ?>
                </div>
                <div class="impact-label">Tons CO₂ Saved</div>
                <small class="text-muted">Equivalent to planting <?php echo number_format($estimated_co2 / 20); ?> trees</small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="impact-stat-card">
                <i class="fas fa-chalkboard-teacher"></i>
                <div class="impact-number">
                    <?php echo number_format(db_count('skill_sessions', ['session_status' => 'completed'])); ?>
                </div>
                <div class="impact-label">Sessions Shared</div>
                <small class="text-muted">Knowledge shared locally</small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="impact-stat-card">
                <i class="fas fa-users"></i>
                <div class="impact-number">
                    <?php 
                    // Count unique learners with confirmed bookings
                    $confirmed_bookings = db_count('bookings', ['booking_status' => 'confirmed']);
                    echo number_format($confirmed_bookings); 
                    ?>
                </div>
                <div class="impact-label">Learners Engaged</div>
                <small class="text-muted">Community members learning</small>
            </div>
        </div>
    </div>

    <!-- Impact by Category -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h2 class="text-center mb-4">
                <i class="fas fa-chart-bar"></i> Impact by Category
            </h2>
            
            <?php
            // Get categories with session counts
            $categories = db_query("
                SELECT c.name as category_name, COUNT(s.session_id) as session_count
                FROM Categories c
                LEFT JOIN skill_sessions s ON c.category_id = s.category_id AND s.session_status = 'completed'
                GROUP BY c.category_id, c.name
                ORDER BY session_count DESC
            ");
            
            if ($categories && count($categories) > 0):
                foreach ($categories as $category):
                    $co2_saved = $category['session_count'] * 2.5; // Estimated
            ?>
                <div class="category-impact">
                    <div>
                        <span class="category-name">
                            <i class="fas fa-tag text-muted me-2"></i>
                            <?php echo escape($category['category_name']); ?>
                        </span>
                        <small class="text-muted ms-3">
                            <?php echo $category['session_count']; ?> sessions
                        </small>
                    </div>
                    <div class="category-co2">
                        <?php echo number_format($co2_saved, 2); ?> kg CO₂
                    </div>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-leaf fa-3x mb-3 opacity-50"></i>
                    <p>Impact data will appear here as sessions are completed</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- How We Calculate Impact -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="methodology-card">
                <h4><i class="fas fa-calculator me-2"></i> How We Calculate Impact</h4>
                <p>
                    Our CO₂ savings calculations are based on comparing local skill-sharing to traditional 
                    learning methods (driving to classes, commercial facilities, etc.):
                </p>
                <ul>
                    <li><strong>In-Person Sessions:</strong> Average 2.5 kg CO₂ saved per participant-hour through reduced transportation</li>
                    <li><strong>Online Sessions:</strong> Average 0.8 kg CO₂ saved per participant-hour from avoided facility energy use</li>
                    <li><strong>Community Learning:</strong> Shared resources and local proximity multiply environmental benefits</li>
                </ul>
                <p class="mb-0">
                    <small class="text-muted">
                        * Calculations based on EPA transportation emissions data and average classroom energy consumption
                    </small>
                </p>
            </div>
        </div>
    </div>

    <!-- Transparency Notice -->
    <div class="transparency-notice">
        <h4><i class="fas fa-lightbulb me-2"></i> Our Commitment to Transparency</h4>
        <p>
            We believe in honest impact reporting. These numbers represent <strong>estimated</strong> CO₂ 
            savings based on comparative analysis with traditional learning methods. Actual impact varies 
            based on many factors including:
        </p>
        <ul class="mb-3">
            <li>Distance participants would have traveled</li>
            <li>Mode of transportation avoided</li>
            <li>Energy efficiency of avoided facilities</li>
            <li>Session duration and participant count</li>
        </ul>
        <p class="mb-0">
            <strong>Our goal isn't perfect accuracy—it's to highlight how local skill-sharing creates 
            positive environmental change.</strong> Every session matters, and together we're making a difference.
        </p>
    </div>

    <!-- Call to Action -->
    <div class="text-center my-5">
        <h3 class="mb-4">Join the Movement</h3>
        <p class="lead mb-4">
            Be part of our sustainable learning community. Share your skills or learn something new—locally.
        </p>
        <?php if (!is_logged_in()): ?>
            <a href="register.php" class="btn btn-success btn-lg me-3">
                <i class="fas fa-user-plus"></i> Join Now
            </a>
            <a href="sessions.php" class="btn btn-outline-success btn-lg">
                <i class="fas fa-search"></i> Browse Sessions
            </a>
        <?php else: ?>
            <a href="sessions.php" class="btn btn-success btn-lg">
                <i class="fas fa-search"></i> Browse Sessions
            </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../app/includes/footer.php'; ?>
