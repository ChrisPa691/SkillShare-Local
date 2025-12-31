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

<style>
    /* Navigation Bar Styles */
    .main-navbar {
        position: fixed;
        top: 80px; /* Below header */
        left: 0;
        right: 0;
        z-index: 1020;
        background: linear-gradient(135deg, #28a745 0%, #17a2b8 100%);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: top 0.3s ease;
    }
    
    .main-navbar.shrink {
        top: 50px; /* Adjust when header shrinks */
    }
    
    .navbar-brand {
        font-weight: 600;
        color: white !important;
        display: none; /* Hide on desktop, show on mobile */
    }
    
    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        padding: 12px 20px !important;
        margin: 0 5px;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white !important;
        transform: translateY(-2px);
    }
    
    .navbar-nav .nav-link.active {
        background-color: rgba(255, 255, 255, 0.3);
        color: white !important;
        font-weight: 600;
    }
    
    .navbar-nav .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 20px;
        right: 20px;
        height: 2px;
        background: white;
        border-radius: 2px;
    }
    
    .navbar-nav .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
    }
    
    /* User Profile Dropdown */
    .user-dropdown .dropdown-toggle {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white !important;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .user-dropdown .dropdown-toggle:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: white;
    }
    
    .user-dropdown .dropdown-menu {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        margin-top: 10px;
    }
    
    .user-dropdown .dropdown-item {
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    
    .user-dropdown .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(23, 162, 184, 0.1) 100%);
        padding-left: 25px;
    }
    
    .user-dropdown .dropdown-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    /* Mobile Navbar */
    .navbar-toggler {
        border: 2px solid white;
        padding: 8px 12px;
    }
    
    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.3);
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    @media (max-width: 991px) {
        .navbar-brand {
            display: block;
        }
        
        .main-navbar {
            top: 100px;
        }
        
        .main-navbar.shrink {
            top: 70px;
        }
        
        .navbar-collapse {
            background: rgba(0, 0, 0, 0.1);
            padding: 15px;
            border-radius: 15px;
            margin-top: 10px;
        }
        
        .navbar-nav .nav-link {
            margin: 5px 0;
        }
    }
</style>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg main-navbar" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-leaf me-2"></i>SkillShare Local
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
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
                        <a class="nav-link <?php echo navbar_is_active('learner_dashboard.php'); ?>" href="learner_dashboard.php">
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
                        <a class="nav-link <?php echo navbar_is_active('instructor_dashboard.php'); ?>" href="instructor_dashboard.php">
                            <i class="fas fa-home"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('my_sessions.php'); ?>" href="my_sessions.php">
                            <i class="fas fa-chalkboard-teacher"></i>My Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('create_session.php'); ?>" href="create_session.php">
                            <i class="fas fa-plus-circle"></i>Create Session
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('session_bookings.php'); ?>" href="session_bookings.php">
                            <i class="fas fa-users"></i>Bookings
                        </a>
                    </li>
                    
                <?php elseif ($user_role === 'admin'): ?>
                    <!-- Admin Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('admin_dashboard.php'); ?>" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('manage_users.php'); ?>" href="manage_users.php">
                            <i class="fas fa-users-cog"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo navbar_is_active('manage_sessions.php'); ?>" href="manage_sessions.php">
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
