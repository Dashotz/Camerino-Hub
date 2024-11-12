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

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-4 mb-4">
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
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="mb-3">Student Portal</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="student_dashboard.php">Dashboard</a></li>
                        <li><a href="student_courses.php">My Classes</a></li>
                        <li><a href="student_profile.php">Profile</a></li>
                        <li><a href="student_grades.php">Grades</a></li>
                        <li><a href="student_calendar.php">Calendar</a></li>
                    </ul>
                </div>

                <!-- Government Links -->
                <div class="col-lg-4 col-md-4 mb-4">
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
                    <div class="social-links mt-3">
                        <a href="#" class="social-link bg-white">
                            <i class="fab fa-facebook-f text-primary"></i>
                        </a>
                        <a href="#" class="social-link bg-white">
                            <i class="fab fa-twitter text-primary"></i>
                        </a>
                        <a href="#" class="social-link bg-white">
                            <i class="fab fa-youtube text-primary"></i>
                        </a>
                        <a href="#" class="social-link bg-white">
                            <i class="fab fa-instagram text-primary"></i>
                        </a>
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
        margin-top: 15px;
    }
    
    .bottom-footer {
        text-align: center;
    }
    
    .bottom-footer .text-md-right {
        text-align: center !important;
        margin-top: 10px;
    }
}

/* Make sure icons are visible */
.fab {
    font-size: 16px;
    line-height: 1;
}
</style>
