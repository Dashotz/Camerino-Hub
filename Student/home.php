<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Check for active session and device status
if ($isLoggedIn) {
    require_once('../db/dbConnector.php');
    $db = new DbConnector();
    
    $student_id = $_SESSION['id'];
    
    // Check if user is logged in on another device - using only user_online first
    $check_session_query = "SELECT user_online FROM student WHERE student_id = ?";
    $stmt = $db->prepare($check_session_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_status = $result->fetch_assoc();

    // Generate or get session ID
    if (!isset($_SESSION['session_id'])) {
        $_SESSION['session_id'] = session_id();
    }

    // Update user status and session
    $update_status = "UPDATE student SET 
        user_online = 1, 
        session_id = ? 
        WHERE student_id = ?";
    $stmt = $db->prepare($update_status);
    $stmt->bind_param("si", $_SESSION['session_id'], $student_id);
    $stmt->execute();

    // Check if this is a different session
    $check_session = "SELECT session_id FROM student WHERE student_id = ?";
    $stmt = $db->prepare($check_session);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_session = $result->fetch_assoc();

    if ($current_session['session_id'] !== $_SESSION['session_id']) {
        // Force logout
        session_destroy();
        header("Location: Student-Login.php?error=multiple_login");
        exit();
    }
}

// Only redirect if trying to access protected pages
if (isset($requireLogin) && $requireLogin && !$isLoggedIn) {
    header("Location: Student-Login.php");
    exit();
}

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
    <link rel="stylesheet" href="css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
	<link rel="icon" href="../images/light-logo.png">
</head>
<body>
    <!-- Header and Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
        <a class="navbar-brand" href="home.php">
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
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="student_dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="student_profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn-signup" href="../login.php">Log In</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero section-gap">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text animate__animated animate__fadeInLeft">
                    <h1>Welcome<?php echo $isLoggedIn ? ', ' . htmlspecialchars($userData['firstname']) : ''; ?> to<br>
                        <span class="highlight">Gov D.M. Camerino High School</span>
                    </h1>
                    <p class="lead animate__animated animate__fadeInLeft animate__delay-1s">Nurturing Excellence, Building Character, Shaping Future Leaders</p>
                    <div class="cta-buttons animate__animated animate__fadeInUp animate__delay-2s">
                        <?php if ($isLoggedIn): ?>
                            <a href="student_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            <a href="student_section.php" class="btn btn-outline-primary">My Classes</a>
                        <?php else: ?>
                            <a href="Student-Login.php" class="btn btn-primary">Student Portal</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image animate__animated animate__fadeInRight">
                    <img src="../images/student.png" alt="High School Students">
                </div>
            </div>
            
            <?php if ($isLoggedIn): ?>
                <div class="search-container animate__animated animate__fadeInUp animate__delay-3s">
                    <form action="search_results.php" method="GET" class="search-form">
                        <input type="text" name="query" id="searchInput" placeholder="Search something..." required>
                    </form>
                </div>
            <?php else: ?>
                <div class="search-container animate__animated animate__fadeInUp animate__delay-3s">
                    <input type="text" id="searchInput" placeholder="Please login to access search features" disabled>
                </div>
            <?php endif; ?>
            
            <div class="quick-links animate__animated animate__fadeInUp animate__delay-4s">
                <p>You may be looking for</p>
                <div class="links">
                    <a href="home.php" class="link-item active">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="site-map.php" class="link-item">
                        <i class="fas fa-map"></i>
                        <span>Site Map</span>
                    </a>
                    <a href="News.php" class="link-item">
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

    <!-- School Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose Gov D.M. Camerino</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>Quality Education</h3>
                    <p>Comprehensive junior high school curriculum aligned with DepEd standards</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Student Development</h3>
                    <p>Holistic approach to academic, social, and personal growth</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Excellence in Sports</h3>
                    <p>Comprehensive athletics program and modern sports facilities</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>Arts & Culture</h3>
                    <p>Rich programs in music, visual arts, and performing arts</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section Update -->
    <section class="statistics-section">
        <div class="container">
            <div class="section-header">
                <h2>Our School at a Glance</h2>
                <p>Building tomorrow's leaders today</p>
            </div>
            
            <div class="statistics-grid">
                <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-count="2500">0</div>
                        <h3>Students</h3>
                        <p>Grades 7-10 learners</p>
                    </div>
                    <div class="stat-footer">
                        <span class="trend positive">
                            <i class="fas fa-arrow-up"></i> Growing community
                        </span>
                    </div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-count="120">0</div>
                        <h3>Teachers</h3>
                        <p>Dedicated educators</p>
                    </div>
                    <div class="stat-footer">
                        <span class="trend positive">
                            <i class="fas fa-star"></i> DepEd certified
                        </span>
                    </div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-count="50">0</div>
                        <h3>Awards</h3>
                        <p>Academic & sports achievements</p>
                    </div>
                    <div class="stat-footer">
                        <span class="trend positive">
                            <i class="fas fa-plus"></i> Regional & national recognition
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="programs-section">
        <div class="container">
            <h2 class="section-title">Academic Programs</h2>
            <div class="programs-grid">
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-atom"></i>
                    </div>
                    <h3>Science Program</h3>
                    <ul>
                        <li>Advanced Mathematics</li>
                        <li>Laboratory Sciences</li>
                        <li>Research Projects</li>
                    </ul>
                </div>

                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Technology Program</h3>
                    <ul>
                        <li>Computer Education</li>
                        <li>Digital Literacy</li>
                        <li>Basic Programming</li>
                    </ul>
                </div>

                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Arts Program</h3>
                    <ul>
                        <li>Visual Arts</li>
                        <li>Performing Arts</li>
                        <li>Music Education</li>
                    </ul>
                </div>

                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <h3>Sports Program</h3>
                    <ul>
                        <li>Physical Education</li>
                        <li>Team Sports</li>
                        <li>Athletics</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Photo Section -->
    <section class="photos full-width section-gap">
        <div class="container">
            <h2>Photo Gallery</h2>
            <div class="custom-carousel" id="photoCarousel">
                <div class="carousel-container">
                    <div class="carousel-slide">
                        <img src="../images/1.jpg" alt="School Photo 1">
                    </div>
                    <div class="carousel-slide">
                        <img src="../images/2.jpg" alt="School Photo 2">
                    </div>
                    <div class="carousel-slide">
                        <img src="../images/3.jpg" alt="School Photo 3">
                    </div>
                </div>
                <button class="carousel-button prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-button next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Our School Officials Section -->
    <section class="officials-section">
        <div class="container">
            <h2 class="section-title">Our School Officials</h2>
            <div class="officials-carousel owl-carousel owl-theme">
                <!-- Principal -->
                <div class="official-card">
                    <div class="official-image">
                        <img src="../images/teacherbg.png" alt="School Principal">
                        <div class="official-overlay">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="official-info">
                        <h4>Dr. Jane Doe</h4>
                        <p class="position">School Principal</p>
                        <p class="credentials">Ph.D. in Educational Leadership</p>
                    </div>
                </div>

                <!-- Assistant Principal -->
                <div class="official-card">
                    <div class="official-image">
                        <img src="../images/teacherbg.png" alt="Assistant Principal">
                        <div class="official-overlay">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="official-info">
                        <h4>Mr. John Smith</h4>
                        <p class="position">Assistant Principal</p>
                        <p class="credentials">M.A. in Education Administration</p>
                    </div>
                </div>

                <!-- Add more officials as needed -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once('includes/footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('#photoCarousel .carousel-container');
        const slides = carousel.querySelectorAll('.carousel-slide');
        const prevButton = document.querySelector('#photoCarousel .prev');
        const nextButton = document.querySelector('#photoCarousel .next');
        let currentIndex = 0;

        // Create indicators
        const indicatorsContainer = document.createElement('div');
        indicatorsContainer.className = 'carousel-indicators';
        slides.forEach((_, index) => {
            const indicator = document.createElement('div');
            indicator.className = `carousel-indicator ${index === 0 ? 'active' : ''}`;
            indicator.addEventListener('click', () => goToSlide(index));
            indicatorsContainer.appendChild(indicator);
        });
        document.querySelector('#photoCarousel').appendChild(indicatorsContainer);

        function updateIndicators() {
            document.querySelectorAll('.carousel-indicator').forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentIndex);
            });
        }

        function showSlide(index, direction = 'next') {
            // Remove existing animation classes
            slides.forEach(slide => {
                slide.classList.remove('slide-enter', 'slide-enter-active', 'slide-exit', 'slide-exit-active');
            });

            // Add new animation classes
            const currentSlide = slides[currentIndex];
            const nextSlide = slides[index];

            if (direction === 'next') {
                currentSlide.classList.add('slide-exit');
                nextSlide.classList.add('slide-enter');
            } else {
                currentSlide.classList.add('slide-exit');
                nextSlide.classList.add('slide-enter');
            }

            // Trigger animation
            setTimeout(() => {
                currentSlide.classList.add('slide-exit-active');
                nextSlide.classList.add('slide-enter-active');
            }, 50);

            // Update transform
            carousel.style.transform = `translateX(-${index * 100}%)`;
            currentIndex = index;
            updateIndicators();
        }

        function nextSlide() {
            const nextIndex = (currentIndex + 1) % slides.length;
            showSlide(nextIndex, 'next');
        }

        function prevSlide() {
            const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(prevIndex, 'prev');
        }

        function goToSlide(index) {
            const direction = index > currentIndex ? 'next' : 'prev';
            showSlide(index, direction);
        }

        // Event Listeners
        nextButton.addEventListener('click', nextSlide);
        prevButton.addEventListener('click', prevSlide);

        // Touch/Swipe Support
        let touchStartX = 0;
        let touchEndX = 0;

        carousel.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, false);

        carousel.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        // Keyboard Navigation
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });

        // Auto-play with pause on hover
        let autoplayInterval = setInterval(nextSlide, 5000);

        carousel.addEventListener('mouseenter', () => {
            clearInterval(autoplayInterval);
        });

        carousel.addEventListener('mouseleave', () => {
            autoplayInterval = setInterval(nextSlide, 5000);
        });
    });
    </script>

    <script>
    // Welcome Alert Function
    function showWelcomeAlert() {
        Swal.fire({
            title: '<div class="animate__animated animate__fadeInDown">Welcome to Camerino Hub LMS!</div>',
            html: `
                <div class="welcome-content animate__animated animate__fadeIn">
                    <div class="welcome-icon animate__animated animate__zoomIn">
                        <img src="../images/welcome-student.gif" alt="Welcome" style="width: 120px; margin-bottom: 20px;">
                    </div>
                    <p class="welcome-text">
                        Hello, ${escapeHtml('<?php echo $_SESSION['welcome_name'] ?? ''; ?>')}!
                    </p>
                    <p class="welcome-subtext">
                        Welcome to Gov. D. M. Camerino Learning Management System.
                        Your gateway to digital education excellence.
                    </p>
                    <p class="welcome-features">
                        ✓ Access your courses<br>
                        ✓ Connect with teachers<br>
                        ✓ Track your progress
                    </p>
                </div>
            `,
            showConfirmButton: false,
            timer: 3000, // Auto close after 3 seconds
            timerProgressBar: true,
            customClass: {
                popup: 'welcome-popup animate__animated animate__fadeInUp'
            },
            didOpen: () => {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            },
            willClose: () => {
                // Redirect after closing
                window.location.href = 'student_dashboard.php';
            }
        });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Show alert when page loads if user just logged in
    <?php if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in']): ?>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Small delay to ensure everything is loaded
        setTimeout(() => {
            showWelcomeAlert();
            <?php 
            // Remove the flags after showing the alert
            unset($_SESSION['just_logged_in']);
            unset($_SESSION['welcome_name']);
            ?>
        }, 100);
    });
    <?php endif; ?>
    </script>

    <style>
    .welcome-popup {
        padding: 2rem;
        border-radius: 15px;
        background: linear-gradient(145deg, #ffffff, #f3f4f6);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .welcome-title {
        color: #2c5282;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .welcome-text {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .welcome-subtext {
        color: #4a5568;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    .welcome-features {
        color: #2d3748;
        font-size: 1.1rem;
        text-align: left;
        margin: 1.5rem auto;
        max-width: 300px;
        line-height: 1.8;
    }

    .welcome-button {
        padding: 12px 30px;
        font-size: 1.1rem;
        border-radius: 25px;
        text-transform: uppercase;
        font-weight: 500;
        box-shadow: 0 4px 6px rgba(76, 175, 80, 0.2);
    }

    .welcome-icon {
        margin-bottom: 1.5rem;
    }

    .custom-start-button {
        background: linear-gradient(45deg, #4CAF50, #45a049);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 500;
        text-transform: uppercase;
        box-shadow: 0 4px 6px rgba(76, 175, 80, 0.2);
    }
    </style>


    <script>
    // Initialize Owl Carousel
    $(document).ready(function(){
        $('.officials-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    });
    </script>

    <!-- Statistics Counter Animation -->
    <script>
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        let count = 0;
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps

        const timer = setInterval(() => {
            count += increment;
            if (count >= target) {
                element.textContent = target + '+';
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(count) + '+';
            }
        }, 16);
    }

    // Initialize counters when they come into view
    const observers = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observers.unobserve(entry.target);
            }
        });
    });

    document.querySelectorAll('.stat-number').forEach(counter => {
        observers.observe(counter);
    });
    </script>

    <script>
    function performSearch() {
        const searchTerm = document.getElementById('searchInput').value.trim();
        if (searchTerm) {
            // You can modify this to point to your actual search endpoint
            window.location.href = `search_results.php?q=${encodeURIComponent(searchTerm)}`;
        }
    }

    function redirectToLogin() {
        window.location.href = 'Student-Login.php';
    }

    // Optional: Add enter key support for search
    document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && <?php echo $isLoggedIn ? 'true' : 'false' ?>) {
            performSearch();
        }
    });
    </script>

    <script>
    $(document).ready(function() {
        let searchTimeout;
        const searchInput = $('#searchInput');
        
        // Live search suggestions
        searchInput.on('keyup', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: 'search_suggestions.php',
                        method: 'GET',
                        data: { query: query },
                        success: function(response) {
                            // Handle search suggestions
                            showSuggestions(response);
                        }
                    });
                }, 300);
            }
        });
    });
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
    // Simple activity update function
    function updateActivity() {
        fetch('update_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .catch(error => {
            console.log('Error updating activity:', error);
        });
    }

    // Change from 60000 (1 minute) to 300000 (5 minutes)
    setInterval(updateActivity, 300000);

    // Only update activity when tab becomes visible and was hidden for more than 5 seconds
    let lastHiddenTime = 0;
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            lastHiddenTime = Date.now();
        } else if (Date.now() - lastHiddenTime > 5000) { // Only update if hidden for more than 5 seconds
            updateActivity();
        }
    });
    </script>
</body>
</html>
