<?php
/**
 * Header Include File
 * Contains the HTML head section and opening body tag
 * Includes Bootstrap 5, jQuery, Font Awesome, and custom styles
 */

// Include cookie functions
require_once __DIR__ . '/../config/config.php';

// Determine the base path for assets
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . '/CourseProject';

// Get user preferences from cookies
$user_preferences = getAllPreferences();
$theme = $user_preferences['theme'];
$language = $user_preferences['language'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SkillShare Local - Community-driven sustainable learning platform connecting local instructors and learners">
    <meta name="keywords" content="skill sharing, community learning, sustainability, local education">
    <meta name="author" content="SkillShare Local">
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>SkillShare Local</title>
    
    <!-- Theme Initialization (Before CSS to prevent FOUC) -->
    <script>
        // Load and apply saved theme immediately to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            let theme = savedTheme;
            
            if (theme === 'auto') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                theme = prefersDark ? 'dark' : 'light';
            }
            
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo $base_url; ?>/public/assets/images/ui/favicon.svg">
    <link rel="alternate icon" type="image/x-icon" href="<?php echo $base_url; ?>/public/assets/images/ui/favicon.ico">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery UI CSS (for smooth effects) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/header.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/includes.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/pages.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/theme.css">
    
    <!-- Preferences Script (Currency & Theme) -->
    <script src="<?php echo $base_url; ?>/public/assets/js/preferences.js"></script>
</head>
<body>
    
    <!-- Site Header -->
    <header class="site-header" id="mainHeader">
        <div class="container">
            <div class="header-content">
                <div class="row align-items-center">
                    <!-- Logo Section -->
                    <div class="col-md-6">
                        <div class="logo-section">
                            <i class="fas fa-leaf logo-icon"></i>
                            <div>
                                <h1 class="logo-text">
                                    <span class="highlight">Skill</span>Share <span class="highlight">Local</span>
                                </h1>
                                <p class="tagline">
                                    <i class="fas fa-globe"></i>
                                    Empowering Communities Through Sustainable Learning
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Eco Badge Section -->
                    <div class="col-md-6 text-end d-none d-md-block">
                        <div class="eco-badge">
                            <i class="fas fa-heart"></i>
                            <span>Building a Greener Future Together</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Cookie Consent Banner -->
    <?php if (shouldShowConsentBanner()): ?>
    <div id="cookieConsentBanner" class="cookie-consent-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="consent-content">
                        <i class="fas fa-cookie-bite me-2"></i>
                        <span>
                            <strong>We use cookies</strong> to enhance your experience, analyze site traffic, and personalize content. 
                            By clicking "Accept", you consent to our use of cookies.
                            <a href="#" class="text-white text-decoration-underline">Learn more</a>
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-sm me-2" onclick="handleCookieConsent(false)">
                        <i class="fas fa-times me-1"></i> Decline
                    </button>
                    <button class="btn btn-success btn-sm" onclick="handleCookieConsent(true)">
                        <i class="fas fa-check me-1"></i> Accept
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
