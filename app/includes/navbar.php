<?php
/**
 * Navigation Bar Include File
 * Role-based dynamic navigation menu
 * Requires active session for role detection
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine user role
$user_role = $_SESSION['role'] ?? 'guest';
$user_name = $_SESSION['full_name'] ?? '';

// Get current page for active highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Helper function to check if link is active (using page name)
function navbar_is_active($page) {
    global $current_page;
    return ($current_page === $page) ? 'active' : '';
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg main-navbar" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-leaf me-2"></i>SkillShare Local
        </a>
        
        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" 
                aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Desktop Navigation (hidden on mobile) -->
        <button class="navbar-toggler d-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto">
                
                <?php if ($user_role === 'guest'): ?>
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('index.php'); ?>" href="index.php">
                            <i class="fas fa-home"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('sessions.php'); ?>" href="sessions.php">
                            <i class="fas fa-search"></i>Browse Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('login.php'); ?>" href="login.php">
                            <i class="fas fa-sign-in-alt"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('register.php'); ?>" href="register.php">
                            <i class="fas fa-user-plus"></i>Register
                        </a>
                    </li>
                    
                <?php elseif ($user_role === 'learner'): ?>
                    <!-- Learner Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('dashboard.php'); ?>" href="dashboard.php">
                            <i class="fas fa-home"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('sessions.php'); ?>" href="sessions.php">
                            <i class="fas fa-book-open"></i>Browse Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('my_bookings.php'); ?>" href="my_bookings.php">
                            <i class="fas fa-calendar-check"></i>My Bookings
                        </a>
                    </li>
                    
                <?php elseif ($user_role === 'instructor'): ?>
                    <!-- Instructor Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('dashboard.php'); ?>" href="dashboard.php">
                            <i class="fas fa-home"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('sessions.php'); ?>" href="sessions.php">
                            <i class="fas fa-book-open"></i>Browse Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('session_create.php'); ?>" href="session_create.php">
                            <i class="fas fa-plus-circle"></i>Create Session
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('my_bookings.php'); ?>" href="my_bookings.php">
                            <i class="fas fa-calendar-check"></i>My Bookings
                        </a>
                    </li>
                    
                <?php elseif ($user_role === 'admin'): ?>
                    <!-- Admin Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('dashboard.php'); ?>" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('admin_users.php'); ?>" href="admin_users.php">
                            <i class="fas fa-users-cog"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('admin_sessions.php'); ?>" href="admin_sessions.php">
                            <i class="fas fa-clipboard-list"></i>Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('impact_dashboard.php'); ?>" href="impact_dashboard.php">
                            <i class="fas fa-chart-line"></i>Impact
                        </a>
                    </li>
                    
                <?php endif; ?>
                
            </ul>
            
            <!-- User Dropdown (for logged-in users) -->
            <?php if ($user_role !== 'guest'): ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown user-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($user_name); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog"></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <?php endif; ?>
            
        </div>
    </div>
</nav>

<!-- Off-Canvas Mobile Menu -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileMenuLabel">
            <i class="fas fa-leaf me-2"></i>SkillShare Local
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- User Info Section (if logged in) -->
        <?php if ($user_role !== 'guest'): ?>
        <div class="mobile-user-info mb-4">
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3">
                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0"><?php echo htmlspecialchars($user_name); ?></h6>
                    <small class="text-muted"><?php echo ucfirst($user_role); ?></small>
                </div>
            </div>
        </div>
        <hr>
        <?php endif; ?>
        
        <!-- Navigation Links -->\n        <ul class="nav flex-column mobile-nav-menu">
            <?php if ($user_role === 'guest'): ?>
                <!-- Guest Navigation -->
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('index.php'); ?>" href="index.php">
                        <i class="fas fa-home"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('sessions.php'); ?>" href="sessions.php">
                        <i class="fas fa-search"></i>Browse Sessions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('impact_dashboard.php'); ?>" href="impact_dashboard.php">
                        <i class="fas fa-chart-line"></i>Impact Dashboard
                    </a>
                </li>
                <li><hr class="my-3"></li>
                <li class="nav-item">
                    <a class="nav-link text-primary" href="login.php">
                        <i class="fas fa-sign-in-alt"></i>Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-success" href="register.php">
                        <i class="fas fa-user-plus"></i>Register
                    </a>
                </li>
                
            <?php elseif ($user_role === 'learner'): ?>
                <!-- Learner Navigation -->
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('dashboard.php'); ?>" href="dashboard.php">
                        <i class="fas fa-home"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('sessions.php'); ?>" href="sessions.php">
                        <i class="fas fa-book-open"></i>Browse Sessions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('my_bookings.php'); ?>" href="my_bookings.php">
                        <i class="fas fa-calendar-check"></i>My Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('impact_dashboard.php'); ?>" href="impact_dashboard.php">
                        <i class="fas fa-chart-line"></i>Impact Dashboard
                    </a>
                </li>
                
            <?php elseif ($user_role === 'instructor'): ?>
                <!-- Instructor Navigation -->
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('dashboard.php'); ?>" href="dashboard.php">
                        <i class="fas fa-home"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('sessions.php'); ?>" href="sessions.php">
                        <i class="fas fa-book-open"></i>Browse Sessions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('session_create.php'); ?>" href="session_create.php">
                        <i class="fas fa-plus-circle"></i>Create Session
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('booking_manage.php'); ?>" href="booking_manage.php">
                        <i class="fas fa-tasks"></i>Manage Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('impact_dashboard.php'); ?>" href="impact_dashboard.php">
                        <i class="fas fa-chart-line"></i>Impact Dashboard
                    </a>
                </li>
                
            <?php elseif ($user_role === 'admin'): ?>
                <!-- Admin Navigation -->
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('dashboard.php'); ?>" href="dashboard.php">
                        <i class="fas fa-home"></i>Dashboard
                    </a>
                </li>
                <li><hr class="my-2"></li>
                <li class="nav-item">
                    <small class="text-muted ms-3">Admin Tools</small>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('admin_users.php'); ?>" href="admin_users.php">
                        <i class="fas fa-users"></i>Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('admin_sessions.php'); ?>" href="admin_sessions.php">
                        <i class="fas fa-chalkboard"></i>Manage Sessions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('admin_bookings.php'); ?>" href="admin_bookings.php">
                        <i class="fas fa-bookmark"></i>View Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('admin_impact_factors.php'); ?>" href="admin_impact_factors.php">
                        <i class="fas fa-leaf"></i>Impact Factors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('admin_settings.php'); ?>" href="admin_settings.php">
                        <i class="fas fa-cog"></i>Settings
                    </a>
                </li>
                <li><hr class="my-2"></li>
                <li class="nav-item">
                    <a class="nav-link <?php echo navbar_is_active('impact_dashboard.php'); ?>" href="impact_dashboard.php">
                        <i class="fas fa-chart-line"></i>Impact Dashboard
                    </a>
                </li>
                
            <?php endif; ?>
        </ul>
        
        <!-- Bottom Actions (for logged-in users) -->
        <?php if ($user_role !== 'guest'): ?>
        <hr class="my-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i>My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">
                    <i class="fas fa-cog"></i>Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </li>
        </ul>
        <?php endif; ?>
    </div>
</div>
