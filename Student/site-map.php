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
    <title>Site Map - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="css/site-map.css">
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
                    <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
                    
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
                    <h1>Site Map and Location<br><span class="highlight">Gov D.M. Camerino</span></h1>
                    <p class="lead">Find your way around our campus and discover our facilities</p>
                    <div class="cta-buttons">
                        <?php if ($isLoggedIn): ?>
                            <a href="student_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            <a href="student_courses.php" class="btn btn-outline-primary">My Courses</a>
                        <?php else: ?>
                            <a href="Student-Login.php" class="btn btn-primary">Login Now</a>
                            <a href="student_registration.php" class="btn btn-outline-primary">Enroll Now!</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="../images/student.png" alt="Students">
                </div>
            </div>
            
            <div class="search-container">
                <input type="text" placeholder="Search something...">
                <button class="btn-search">Search</button>
            </div>
            
            <div class="quick-links">
                <p>You may be looking for</p>
                <div class="links">
                <a href="home.php" class="link-item">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="site-map.php" class="link-item active">
                        <i class="fas fa-map"></i>
                        <span>Site Map</span>
                    </a>
                    <a href="news.php" class="link-item">
                        <i class="fas fa-newspaper"></i>
                        <span>Updates</span>
                    </a>
                    <a href="aboutus.php" class="link-item">
                        <i class="fas fa-info-circle"></i>
                        <span>About Us</span>
                    </a>
                    <a href="contactus.php" class="link-item">
                        <i class="fas fa-envelope"></i>
                        <span>Contact Us</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
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

    <!-- School Officials Section -->
    <section class="officials full-width section-gap">
        <div class="container">
            <h2>Our School Officials</h2>
            <div id="officialsCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 1">
                                    <h3>Tom Hiddleston</h3>
                                    <p>Campus Registrar</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 2">
                                    <h3>Jesus V. Bergado</h3>
                                    <p>Principal</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 3">
                                    <h3>Dr. Leona Uy</h3>
                                    <p>Campus Administrator</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 4">
                                    <h3>River Phoenix</h3>
                                    <p>Campus Director</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more carousel items if needed -->
                </div>
                <ol class="carousel-indicators">
                    <li data-target="#officialsCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#officialsCarousel" data-slide-to="1"></li>
                    <li data-target="#officialsCarousel" data-slide-to="2"></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- School Statistics Section -->
    <section class="school-statistics bg-light py-5 section-gap">
        <div class="container">
            <h2 class="text-center mb-5">Our School at a Glance</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                            <h3 class="card-title">1200+</h3>
                            <p class="card-text">Enrolled Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-book fa-3x text-danger mb-3"></i>
                            <h3 class="card-title">200+</h3>
                            <p class="card-text">Academic Programs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                            <h3 class="card-title">600+</h3>
                            <p class="card-text">Faculty and Staff</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leave a Comment and Suggestion Section -->
    <section class="comment-suggestion section-gap">
        <div class="container">
            <div class="logo-bar">
                <img src="images/Logo.png" alt="DepEd Logo">
                <img src="images/Logo.png" alt="Bagong Pilipinas Logo">
                <img src="images/Logo.png" alt="Seal 1">
                <img src="images/Logo.png" alt="Seal 2">
                <img src="images/Logo.png" alt="Seal 3">
            </div>
            <h2>Leave a <span class="text-primary">Comment</span> and <span class="text-primary">Suggestion</span></h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="image-container">
                        <img src="images/students.jpg" alt="Students" class="img-fluid">
                        <div class="overlay">
                            <span class="emoji">😊</span>
                            <span class="text">84k+</span>
                            <span class="subtext">We're happy to help!</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form id="commentForm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="firstName" 
                                       value="<?php echo htmlspecialchars($userData['firstname'] ?? ''); ?>"
                                       placeholder="First Name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="lastName" 
                                       value="<?php echo htmlspecialchars($userData['lastname'] ?? ''); ?>"
                                       placeholder="Last Name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" 
                                   value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>"
                                   placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="comment" rows="4" 
                                      placeholder="Comment" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('#photoCarousel .carousel-container');
        const slides = carousel.querySelectorAll('.carousel-slide');
        const prevButton = document.querySelector('#photoCarousel .prev');
        const nextButton = document.querySelector('#photoCarousel .next');
        let currentIndex = 0;

        function showSlide(index) {
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        }

        nextButton.addEventListener('click', nextSlide);
        prevButton.addEventListener('click', prevSlide);

        // Optional: Auto-play
        setInterval(nextSlide, 5000);
    });
    </script>
</body>
</html>




