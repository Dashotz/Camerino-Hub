<!-- Footer -->
<footer>
    <!-- Newsletter Section -->
    <div class="newsletter-section py-4" style="background-color: #1e3a8a;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0 text-white">Subscribe to Our Newsletter</h4>
                    <p class="mb-0 text-white-50">Stay updated with school news and announcements</p>
                </div>
                <div class="col-md-6">
                    <form class="newsletter-form d-flex">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button type="submit" class="btn btn-warning ml-2">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Content -->
    <div class="main-footer py-5" style="background-color: #2563eb;">
        <div class="container">
            <div class="row">
                <!-- School Info -->
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand d-flex align-items-center mb-3">
                        <img src="../images/logo.png" alt="School Logo" class="footer-logo mr-2" style="width: 50px;">
                        <div>
                            <h5 class="mb-0">Gov D.M. Camerino</h5>
                            <small>National High School</small>
                        </div>
                    </div>
                    <p>Nurturing Excellence, Building Character, Shaping Future Leaders</p>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Medicion 2, A.Imus City, Cavite 4103</p>
                        <p><i class="fas fa-phone mr-2"></i>(046) 456-5874</p>
                        <p><i class="fas fa-envelope mr-2"></i>profcamerino@yahoo.com</p>
                    </div>
                </div>

                <!-- Links Grid Section -->
                <div class="col-lg-8">
                    <div class="footer-links-grid">
                        <!-- Quick Links -->
                        <div class="links-section">
                            <h5 class="mb-3">Quick Links</h5>
                            <ul class="list-unstyled footer-links">
                                <li><a href="home.php">Home</a></li>
                                <li><a href="aboutus.php">About Us</a></li>
                                <li><a href="News.php">News & Updates</a></li>
                                <li><a href="site-map.php">Site Map</a></li>
                                <li><a href="contactus.php">Contact Us</a></li>
                            </ul>
                        </div>

                        <!-- Student Portal -->
                        <div class="links-section">
                            <h5 class="mb-3">Student Portal</h5>
                            <ul class="list-unstyled footer-links">
                                <li><a href="student_dashboard.php">Dashboard</a></li>
                                <li><a href="student_section.php">My Section</a></li>
                                <li><a href="student_profile.php">Profile</a></li>
                                <li><a href="student_grades.php">Grades</a></li>
                                <li><a href="student_calendar.php">Calendar</a></li>
                            </ul>
                        </div>

                        <!-- Government Links -->
                        <div class="links-section">
                            <h5 class="mb-3">Government Links</h5>
                            <ul class="list-unstyled footer-links">
                                <li><a href="https://lis.deped.gov.ph/" target="_blank">
                                    <i class="fas fa-external-link-alt mr-2"></i>Learner Information System
                                </a></li>
                                <li><a href="https://calabarzon.deped.gov.ph/" target="_blank">
                                    <i class="fas fa-external-link-alt mr-2"></i>DepEd CALABARZON
                                </a></li>
                                <li><a href="https://www.deped.gov.ph/" target="_blank">
                                    <i class="fas fa-external-link-alt mr-2"></i>Department of Education
                                </a></li>
                            </ul>
                            <h5 class="mb-3 mt-4">Connect With Us</h5>
                            <div class="social-links-grid">
                                <a href="https://www.facebook.com/DepEdTayoGDMCIS107985" class="social-link" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="bottom-footer py-3" style="background-color: #1e3a8a;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Gov D.M. Camerino National High School. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                        <li class="list-inline-item"><a href="#">Terms of Use</a></li>
                        <li class="list-inline-item"><a href="#">Sitemap</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- Add this CSS to your stylesheet -->
<style>
.footer-links a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: #fff;
    text-decoration: none;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #ffffff !important;
    transition: all 0.3s ease;
}

.social-link i {
    color: #2563eb; /* Match the main footer background color */
}

.social-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.social-link:hover i {
    color: #1e3a8a; /* Darker blue on hover */
}

.newsletter-form .form-control {
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
}

.newsletter-form .btn {
    border-radius: 25px;
    padding: 10px 25px;
    background-color: #fbbf24;
    color: #1e3a8a;
    border: none;
}

.newsletter-form .btn:hover {
    background-color: #f59e0b;
}

.bg-primary-dark {
    background-color: #1e3a8a;
}

.footer-logo {
    filter: brightness(0) invert(1);
}

.contact-info i {
    width: 20px;
}

.bottom-footer a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.3s ease;
}

.bottom-footer a:hover {
    color: #fff;
}

@media (max-width: 768px) {
    .newsletter-form {
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }
    
    .newsletter-form .btn {
        width: 100%;
    }
    
    .newsletter-section .text-white,
    .newsletter-section .text-white-50 {
        text-align: center;
    }
    
    .footer-brand {
        justify-content: center;
        text-align: center;
    }
    
    .contact-info {
        text-align: center;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .bottom-footer {
        text-align: center;
    }
    
    .bottom-footer .text-md-right {
        text-align: center !important;
        margin-top: 10px;
    }
    
    .list-inline {
        margin-top: 15px;
    }
}

@media (max-width: 576px) {
    .main-footer h5 {
        margin-top: 20px;
        text-align: center;
    }
    
    .footer-links {
        text-align: center;
    }
    
    .contact-info p {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .newsletter-section h4 {
        font-size: 1.25rem;
    }
    
    .newsletter-section p {
        font-size: 0.9rem;
    }
}

/* Make sure icons are visible */
.fab {
    font-size: 16px;
    line-height: 1;
}

.footer-links-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 2rem;
}

.links-section {
    min-width: 0;
}

.links-section h5 {
    color: white;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    text-align: left;
}

.links-section ul {
    padding: 0;
}

.links-section ul li {
    margin-bottom: 0.5rem;
    text-align: left;
}

/* Prevent text overflow */
.footer-links a {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.9rem;
}

@media (max-width: 991px) {
    .footer-links-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    
    .links-section h5 {
        font-size: 1rem;
    }
    
    .footer-links a {
        font-size: 0.85rem;
    }
}

@media (max-width: 768px) {
    .footer-links-grid {
        grid-template-columns: 1fr 1fr 1fr; /* Keep 3 columns but allow content to wrap */
        gap: 10px;
    }
    
    .links-section {
        padding: 0 3px;
    }
    
    .footer-links a {
        font-size: 0.75rem;
        white-space: normal; /* Allow text to wrap */
        overflow: visible; /* Show all content */
        line-height: 1.2; /* Add some line height for better readability */
        margin-bottom: 8px; /* Add space between wrapped items */
    }
}

@media (max-width: 576px) {
    .footer-links-grid {
        grid-template-columns: repeat(3, 1fr); /* Keep 3 columns */
        gap: 8px;
    }
    
    .links-section h5 {
        font-size: 0.8rem;
        margin-bottom: 0.4rem;
    }
    
    .footer-links a {
        font-size: 0.7rem;
        white-space: normal;
        overflow: visible;
    }
    
    .fa-external-link-alt {
        font-size: 0.7rem;
    }
}

/* Social links grid styles */
.social-links-grid {
    display: grid;
    grid-template-columns: repeat(4, 40px);
    gap: 15px;
    justify-content: start;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .social-links-grid {
        grid-template-columns: repeat(4, 30px);
        gap: 10px;
    }
}

/* Additional fixes for very small screens */
@media (max-width: 360px) {
    .footer-links-grid {
        gap: 5px;
    }
    
    .links-section {
        padding: 0 2px;
    }
    
    .footer-links a {
        font-size: 0.65rem;
    }
    
    .links-section h5 {
        font-size: 0.75rem;
    }
}

/* Ensure icons are visible */
.fab {
    font-size: 16px;
    line-height: 1;
}

@media (max-width: 576px) {
    .fab {
        font-size: 14px;
    }
}
</style>
