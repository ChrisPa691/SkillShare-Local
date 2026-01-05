    <?php
    /**
     * Footer Include File
     * Professional footer with project info, links, and impact statement
     */
    ?>
    
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
                                <a href="https://www.facebook.com/" title="Facebook" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://x.com/" title="Twitter" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.instagram.com/" title="Instagram" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="https://www.linkedin.com/" title="LinkedIn" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
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
    <script src="<?php echo $base_url; ?>/public/assets/js/main.js"></script>
    <script src="<?php echo $base_url; ?>/public/assets/js/footer.js"></script>
    
</body>
</html>
