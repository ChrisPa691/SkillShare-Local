    <?php
    /**
     * Footer Include File
     * Professional footer with project info, links, and impact statement
     */
    ?>
    
    <style>
        /* Footer Styles */
        .site-footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative Elements */
        .site-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #28a745 0%, #17a2b8 50%, #28a745 100%);
        }
        
        .footer-content {
            position: relative;
            z-index: 1;
        }
        
        .footer-section h5 {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-section h5 i {
            font-size: 1.5rem;
        }
        
        .footer-section p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-links a:hover {
            color: #28a745;
            padding-left: 10px;
        }
        
        .footer-links a i {
            color: #17a2b8;
            font-size: 0.9rem;
        }
        
        /* Impact Statement */
        .impact-statement {
            background: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .impact-statement p {
            margin: 0;
            font-style: italic;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .impact-statement i {
            color: #28a745;
            font-size: 1.3rem;
            margin-right: 10px;
        }
        
        /* Social Icons */
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .social-links a:hover {
            background: linear-gradient(135deg, #28a745 0%, #17a2b8 100%);
            border-color: white;
            transform: translateY(-5px) rotate(360deg);
        }
        
        /* Statistics */
        .footer-stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            display: block;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
        }
        
        /* Copyright */
        .footer-bottom {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .footer-bottom p {
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .footer-bottom a {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer-bottom a:hover {
            color: #17a2b8;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .site-footer {
                padding: 40px 0 20px;
                margin-top: 50px;
            }
            
            .footer-section {
                margin-bottom: 30px;
            }
            
            .footer-stats {
                justify-content: center;
            }
        }
    </style>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    
                    <!-- About Section -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>
                                <i class="fas fa-leaf"></i>
                                About SkillShare Local
                            </h5>
                            <p>
                                A community-driven platform connecting local instructors with eager learners, 
                                promoting sustainable knowledge sharing and reducing the carbon footprint of education.
                            </p>
                            
                            <!-- Social Links -->
                            <div class="social-links">
                                <a href="#" title="Facebook" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" title="Twitter" aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" title="Instagram" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" title="LinkedIn" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>
                                <i class="fas fa-link"></i>
                                Quick Links
                            </h5>
                            <ul class="footer-links">
                                <li>
                                    <a href="index.php">
                                        <i class="fas fa-chevron-right"></i>Home
                                    </a>
                                </li>
                                <li>
                                    <a href="sessions.php">
                                        <i class="fas fa-chevron-right"></i>Browse Sessions
                                    </a>
                                </li>
                                <li>
                                    <a href="about.php">
                                        <i class="fas fa-chevron-right"></i>About Us
                                    </a>
                                </li>
                                <li>
                                    <a href="contact.php">
                                        <i class="fas fa-chevron-right"></i>Contact
                                    </a>
                                </li>
                                <li>
                                    <a href="impact_dashboard.php">
                                        <i class="fas fa-chevron-right"></i>Impact Dashboard
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Resources -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>
                                <i class="fas fa-book"></i>
                                Resources
                            </h5>
                            <ul class="footer-links">
                                <li>
                                    <a href="faq.php">
                                        <i class="fas fa-chevron-right"></i>FAQ
                                    </a>
                                </li>
                                <li>
                                    <a href="help.php">
                                        <i class="fas fa-chevron-right"></i>Help Center
                                    </a>
                                </li>
                                <li>
                                    <a href="privacy.php">
                                        <i class="fas fa-chevron-right"></i>Privacy
                                    </a>
                                </li>
                                <li>
                                    <a href="terms.php">
                                        <i class="fas fa-chevron-right"></i>Terms
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Impact Stats -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>
                                <i class="fas fa-chart-line"></i>
                                Our Impact
                            </h5>
                            <div class="footer-stats">
                                <div class="stat-item">
                                    <span class="stat-number">1.2k+</span>
                                    <span class="stat-label">Sessions</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">850+</span>
                                    <span class="stat-label">Learners</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">3.5t</span>
                                    <span class="stat-label">CO₂ Saved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Impact Statement -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="impact-statement">
                            <p>
                                <i class="fas fa-globe-americas"></i>
                                <strong>Sustainability Commitment:</strong> 
                                By choosing local, in-person learning experiences, our community has collectively reduced 
                                carbon emissions equivalent to planting over 4,000 trees. Every skill shared locally 
                                contributes to a greener, more connected future.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Copyright -->
                <div class="row">
                    <div class="col-12">
                        <div class="footer-bottom">
                            <p>
                                &copy; <?php echo date('Y'); ?> <a href="index.php">SkillShare Local</a>. 
                                All rights reserved. | Built with <i class="fas fa-heart text-danger"></i> for sustainable education.
                            </p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </footer>
    
    <!-- jQuery (required for Bootstrap and custom effects) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- jQuery UI (for smooth effects) -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            
            // Shrinking header on scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('#mainHeader').addClass('shrink');
                    $('#mainNavbar').addClass('shrink');
                } else {
                    $('#mainHeader').removeClass('shrink');
                    $('#mainNavbar').removeClass('shrink');
                }
            });
            
            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if(target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 150
                    }, 800, 'easeInOutQuad');
                }
            });
            
            // Animate footer stats on scroll into view
            var statsAnimated = false;
            $(window).scroll(function() {
                if (!statsAnimated) {
                    var footerOffset = $('.footer-stats').offset();
                    if (footerOffset && $(window).scrollTop() + $(window).height() > footerOffset.top) {
                        $('.stat-number').each(function() {
                            $(this).prop('Counter', 0).animate({
                                Counter: $(this).text().replace(/[^0-9.]/g, '')
                            }, {
                                duration: 2000,
                                easing: 'swing',
                                step: function(now) {
                                    var text = $(this).parent().find('.stat-number').text();
                                    var suffix = text.match(/[^0-9.]+$/);
                                    $(this).text(Math.ceil(now) + (suffix ? suffix[0] : ''));
                                }
                            });
                        });
                        statsAnimated = true;
                    }
                }
            });
            
            // Highlight active navigation item
            var currentPath = window.location.pathname.split('/').pop();
            $('.navbar-nav .nav-link').each(function() {
                var linkPath = $(this).attr('href');
                if (linkPath === currentPath || (currentPath === '' && linkPath === 'index.php')) {
                    $(this).addClass('active');
                }
            });
            
            // Add hover effect to social links with jQuery UI
            $('.social-links a').hover(
                function() {
                    $(this).effect('bounce', { times: 1, distance: 5 }, 300);
                }
            );
            
            // Fade in footer sections on load
            $('.footer-section').hide().fadeIn(1000);
            
            // Close mobile navbar on link click
            $('.navbar-nav .nav-link').on('click', function() {
                if ($(window).width() < 992) {
                    $('.navbar-collapse').collapse('hide');
                }
            });
            
        });
    </script>
    
    <!-- Custom Page Scripts -->
    <?php if (isset($custom_scripts)): ?>
        <?php echo $custom_scripts; ?>
    <?php endif; ?>
    
</body>
</html>
