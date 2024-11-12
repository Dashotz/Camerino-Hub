<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Get user data if logged in
$userData = null;
if ($isLoggedIn) {
    require_once('../db/dbConnector.php');
    $db = new DbConnector();
    
    $student_id = $_SESSION['id'];
    $stmt = $db->prepare("SELECT * FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/contactus.css">
</head>
<body>
    <!-- Header and Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
        <a class="navbar-brand" href="#">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
            <span class="logo-text">Gov D.M. Camerino</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="site-map.php">Site Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="News.php">News</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contactus.php">Contact Us</a></li>
                    
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="student_dashboard.php">Dashboard</a>
                                <a class="dropdown-item" href="student_profile.php">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn-signup" href="Student-Login.php">Log In</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero section-gap">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Get in Touch<br><span class="highlight">We're Here to Help</span></h1>
                    <p class="lead">Have questions? We'd love to hear from you.</p>
                </div>
                <div class="hero-image">
                    <img src="../images/contact.png" alt="Contact Us">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="contact-info section-gap">
        <div class="container">
            <div class="row info-cards justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="info-card hover-effect">
                        <div class="icon-circle">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Campus Location</h3>
                        <p>Medicion 2, A.Imus City,<br>Cavite 4103</p>
                        <a href="#map" class="btn btn-primary rounded-pill">View on Map</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="info-card hover-effect">
                        <div class="icon-circle">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3>Contact Numbers</h3>
                        <p class="contact-info-text">
                            <span class="d-block">Landline: (046) 456-5874</span>
                            <span class="d-block">Mobile: +63 912 345 6789</span>
                        </p>
                        <a href="tel:+6346456874" class="btn btn-primary rounded-pill">Call Now</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="info-card hover-effect">
                        <div class="icon-circle">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Email Address</h3>
                        <p class="contact-info-text">
                            <span class="d-block">gdmc@deped.gov.ph</span>
                            <span class="d-block">info@gdmc.edu.ph</span>
                        </p>
                        <a href="mailto:gdmc@deped.gov.ph" class="btn btn-primary rounded-pill">Send Email</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section section-gap">
        <div class="container">
            <div class="contact-wrapper bg-white rounded-lg shadow-lg p-5">
                <div class="row">
                    <div class="col-lg-6 pr-lg-5">
                        <div class="form-header mb-4">
                            <h2 class="section-title">Send us a Message</h2>
                            <p class="text-muted">We'd love to hear from you. Please fill out this form.</p>
                        </div>
                        <form id="contactForm" action="process_contact.php" method="POST" class="contact-form">
                            <div class="form-group floating-label">
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($userData['firstname'] ?? '') . ' ' . 
                                              htmlspecialchars($userData['lastname'] ?? ''); ?>" required>
                                <label for="name">Full Name</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                <label for="email">Email Address</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="text" class="form-control" id="subject" name="subject" required>
                                <label for="subject">Subject</label>
                            </div>
                            <div class="form-group floating-label">
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                <label for="message">Message</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block rounded-pill">
                                Send Message <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-6 pl-lg-5 mt-5 mt-lg-0">
                        <div class="contact-info-side">
                            <div class="office-hours-card bg-light p-4 rounded-lg mb-4">
                                <h3 class="mb-4"><i class="far fa-clock mr-2"></i> Office Hours</h3>
                                <div class="schedule-item d-flex justify-content-between mb-3">
                                    <span class="day font-weight-bold">Monday - Friday</span>
                                    <span class="time">8:00 AM - 5:00 PM</span>
                                </div>
                                <div class="schedule-item d-flex justify-content-between mb-3">
                                    <span class="day font-weight-bold">Saturday</span>
                                    <span class="time">8:00 AM - 12:00 PM</span>
                                </div>
                                <div class="schedule-item d-flex justify-content-between text-danger">
                                    <span class="day font-weight-bold">Sunday</span>
                                    <span class="time">Closed</span>
                                </div>
                            </div>
                            
                            <div class="social-connect text-center">
                                <h3 class="mb-4">Connect With Us</h3>
                                <div class="social-icons-large">
                                    <a href="#" class="social-icon-lg facebook"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" class="social-icon-lg twitter"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-icon-lg instagram"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="social-icon-lg youtube"><i class="fab fa-youtube"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <!-- Map Section -->
    <section class="map-section section-gap bg-light mb-0">
        <div class="container">
            <div class="map-wrapper rounded-lg overflow-hidden shadow">
                <iframe width="100%" height="450" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                    src="https://www.google.com/maps/d/u/0/embed?mid=1IQQpwz2BUIbK_5gMU-n1trHY0ENGoY4&ehbc=2E312F&noprof=1">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once('includes/footer.php'); ?>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Form submission handling
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    alert('Thank you for your message. We will get back to you soon!');
                    $('#contactForm')[0].reset();
                },
                error: function() {
                    alert('There was an error sending your message. Please try again later.');
                }
            });
        });
    });
    </script>
</body>
</html>
