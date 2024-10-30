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
    $query = "SELECT * FROM student WHERE student_id = '$student_id'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_array($result);
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
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
            <div class="row info-cards">
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Visit Us</h3>
                        <p>Medicion 2, A.Imus City,<br>Cavite 4103</p>
                        <a href="#map" class="btn btn-outline-primary btn-sm">View Map</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3>Call Us</h3>
                        <p>Main: (046) 456-5874<br>Mobile: +63 912 345 6789</p>
                        <a href="tel:+6346456874" class="btn btn-outline-primary btn-sm">Call Now</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Email Us</h3>
                        <p>gdmc@deped.gov.ph<br>info@gdmc.edu.ph</p>
                        <a href="mailto:gdmc@deped.gov.ph" class="btn btn-outline-primary btn-sm">Send Email</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form section-gap bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Send us a Message</h2>
                    <form id="contactForm" action="process_contact.php" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($userData['firstname'] ?? '') . ' ' . 
                                          htmlspecialchars($userData['lastname'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h2>Office Hours</h2>
                    <div class="office-hours">
                        <div class="day-time">
                            <span class="day">Monday - Friday</span>
                            <span class="time">8:00 AM - 5:00 PM</span>
                        </div>
                        <div class="day-time">
                            <span class="day">Saturday</span>
                            <span class="time">8:00 AM - 12:00 PM</span>
                        </div>
                        <div class="day-time closed">
                            <span class="day">Sunday</span>
                            <span class="time">Closed</span>
                        </div>
                    </div>
                    
                    <h2 class="mt-5">Follow Us</h2>
                    <div class="social-links">
                        <a href="#" class="social-link facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link youtube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section section-gap">
        <div class="container">
            <h2>Our Location</h2>
            <div class="map-container">
                <iframe width="100%" height="450" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                    src="https://www.google.com/maps/d/u/0/embed?mid=1IQQpwz2BUIbK_5gMU-n1trHY0ENGoY4&ehbc=2E312F&noprof=1">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">Gov D.M. Camerino</h5>
                    <p>Medicion 2, A.Imus City, Cavite 4103</p>
                    <p>+(64) 456 - 5874</p>
                    <p>profcamerino@yahoo.com</p>
                    <div class="social-icons">
                        <a href="#" class="text-light"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Quicklinks</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Home</a></li>
                        <li><a href="#" class="text-light">About Us</a></li>
                        <li><a href="#" class="text-light">Our Gallery</a></li>
                        <li><a href="#" class="text-light">News and Updates</a></li>
                        <li><a href="#" class="text-light">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Government Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Learner Information System</a></li>
                        <li><a href="#" class="text-light">DepEd CALABARZON</a></li>
                        <li><a href="#" class="text-light">DepEd Imus City</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; 2024 All Rights Reserved</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
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
