<?php
/**
 * Header Include File
 * Contains the HTML head section and opening body tag
 * Includes Bootstrap 5, jQuery, Font Awesome, and custom styles
 */

// Determine the base path for assets
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . '/CourseProject';
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
    
    <style>
        /* Custom Header Styles */
        :root {
            --primary-green: #28a745;
            --primary-blue: #17a2b8;
            --accent-teal: #20c997;
            --dark-text: #2c3e50;
            --light-bg: #f8f9fa;
            --gradient-eco: linear-gradient(135deg, #28a745 0%, #17a2b8 100%);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            background-color: var(--light-bg);
            padding-top: 140px; /* Offset for fixed header + navbar */
        }
        
        /* Sticky Header with Shrink Effect */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .site-header.shrink {
            padding: 8px 0 !important;
        }
        
        .site-header.shrink .logo-text {
            font-size: 1.5rem !important;
        }
        
        .site-header.shrink .tagline {
            font-size: 0.75rem !important;
        }
        
        /* Header Content */
        .header-content {
            padding: 15px 0;
            transition: padding 0.3s ease;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 2.5rem;
            background: var(--gradient-eco);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: font-size 0.3s ease;
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-text);
            margin: 0;
            transition: font-size 0.3s ease;
        }
        
        .logo-text .highlight {
            background: var(--gradient-eco);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tagline {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: font-size 0.3s ease;
        }
        
        .tagline i {
            color: var(--primary-green);
        }
        
        /* Eco Badge */
        .eco-badge {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(23, 162, 184, 0.1) 100%);
            border: 2px solid var(--primary-green);
            border-radius: 50px;
            padding: 8px 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-green);
        }
        
        .eco-badge i {
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 120px;
            }
            
            .logo-text {
                font-size: 1.3rem;
            }
            
            .logo-icon {
                font-size: 2rem;
            }
            
            .tagline {
                font-size: 0.75rem;
            }
            
            .eco-badge {
                font-size: 0.7rem;
                padding: 5px 12px;
            }
        }
    </style>
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
