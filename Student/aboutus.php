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
    <title>About Us - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/aboutus.css">
</head>
<body>
    <!-- Header and Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
                <span class="logo-text">Gov D.M. Camerino</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="site-map.php">Site Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="News.php">News</a></li>
                    <li class="nav-item"><a class="nav-link active" href="aboutus.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
                    
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
                    <h1>About Us<br><span class="highlight">Gov D.M. Camerino</span></h1>
                    <p class="lead">Discover our history, vision, and commitment to education</p>
                    <div class="cta-buttons">
                        <?php if ($isLoggedIn): ?>
                            <a href="student_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <?php else: ?>
                            <a href="Student-Login.php" class="btn btn-primary">Login Now</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="../images/student.png" alt="Students">
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="quick-links">
                <p>Learn More About</p>
                <div class="links">
                    <a href="#vision-mission" class="link-item">
                        <i class="fas fa-eye"></i>
                        <span>Vision & Mission</span>
                    </a>
                    <a href="#history" class="link-item">
                        <i class="fas fa-history"></i>
                        <span>Our History</span>
                    </a>
                    <a href="#achievements" class="link-item">
                        <i class="fas fa-trophy"></i>
                        <span>Achievements</span>
                    </a>
                    <a href="#faculty" class="link-item">
                        <i class="fas fa-users"></i>
                        <span>Our Faculty</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision and Mission Section -->
    <section id="vision-mission" class="vision-mission section-gap">
        <div class="container">
            <h2 class="text-center mb-5">Vision & Mission</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card vision-card">
                        <div class="card-body">
                            <h3 class="card-title">Vision</h3>
                            <p class="card-text">
                                We dream of Filipinos who passionately love their country and whose values and competencies enable them to realize their full potential and contribute meaningfully to building the nation.
                            </p>
                            <p class="card-text">
                                As a learner-centered public institution, the Department of Education continuously improves itself to better serve its stakeholders.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card mission-card">
                        <div class="card-body">
                            <h3 class="card-title">Mission</h3>
                            <p class="card-text">
                                To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education where:
                            </p>
                            <ul>
                                <li>Students learn in a child-friendly, gender-sensitive, safe, and motivating environment.</li>
                                <li>Teachers facilitate learning and constantly nurture every learner.</li>
                                <li>Administrators and staff, as stewards of the institution, ensure an enabling and supportive environment for effective learning to happen.</li>
                                <li>Family, community, and other stakeholders are actively engaged and share responsibility for developing life-long learners.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section id="history" class="history-section section-gap">
        <div class="container">
            <h2 class="text-center mb-5">Our History</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="year">1999</div>
                    <div class="content">
                        <h4>Establishment</h4>
                        <p>Gov D.M. Camerino Elementary School was established through Republic Act No. 8711, named after Governor Dominador M. Camerino.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="year">2005</div>
                    <div class="content">
                        <h4>Campus Expansion</h4>
                        <p>Major infrastructure development including new classrooms and modern facilities.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="year">2015</div>
                    <div class="content">
                        <h4>Digital Integration</h4>
                        <p>Implementation of technology-enhanced learning programs and computer laboratories.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="year">2023</div>
                    <div class="content">
                        <h4>Modern Era</h4>
                        <p>Launch of comprehensive Learning Management System and virtual classrooms.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section id="achievements" class="achievements-section section-gap">
        <div class="container">
            <h2 class="text-center mb-5">Our Achievements</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Academic Excellence</h4>
                        <p>Consistently ranked among top schools in the region with outstanding national achievement test scores.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h4>Competition Winners</h4>
                        <p>Multiple awards in regional and national academic competitions, including Science Fair and Mathematics Olympiad.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h4>Quality Education</h4>
                        <p>Recognized by DepEd for outstanding implementation of K-12 curriculum and innovative teaching methods.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty Section -->
    <section id="faculty" class="faculty-section section-gap">
        <div class="container">
            <h2 class="text-center mb-5">Our Faculty</h2>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="faculty-card">
                        <img src="../images/student1.png" alt="Principal" class="faculty-image">
                        <h4>Dr. Maria Santos</h4>
                        <p class="position">School Principal</p>
                        <p class="credentials">Ph.D. in Educational Management</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="faculty-card">
                        <img src="../images/teacher.png" alt="VP Academics" class="faculty-image">
                        <h4>Prof. Juan Dela Cruz</h4>
                        <p class="position">VP for Academics</p>
                        <p class="credentials">M.A. in Education</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="faculty-card">
                        <img src="../images/teacher.png" alt="Science Department Head" class="faculty-image">
                        <h4>Ms. Ana Reyes</h4>
                        <p class="position">Science Department Head</p>
                        <p class="credentials">M.S. in Biology</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="faculty-card">
                        <img src="../images/student1.png" alt="Math Department Head" class="faculty-image">
                        <h4>Mr. Pedro Lim</h4>
                        <p class="position">Mathematics Department Head</p>
                        <p class="credentials">M.S. in Mathematics</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Keep your existing footer -->
    <?php require_once('includes/footer.php'); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
