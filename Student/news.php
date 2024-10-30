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
    <title>News & Updates - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/news.css">
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
                    <li class="nav-item"><a class="nav-link active" href="News.php">News</a></li>
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
                    <h1>News & Updates<br><span class="highlight">Gov D.M. Camerino</span></h1>
                    <p class="lead">Stay informed with the latest news, events, and announcements</p>
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
            
            <!-- Search and Quick Links -->
            <div class="search-container">
                <input type="text" id="newsSearch" placeholder="Search news...">
                <button class="btn-search" onclick="searchNews()">Search</button>
            </div>
            
            <!-- Quick Links -->
            <div class="quick-links">
                <p>News Categories</p>
                <div class="links">
                    <a href="#" class="link-item" data-category="all">
                        <i class="fas fa-globe"></i>
                        <span>All News</span>
                    </a>
                    <a href="#" class="link-item" data-category="academic">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Academic</span>
                    </a>
                    <a href="#" class="link-item" data-category="events">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                    <a href="#" class="link-item" data-category="announcements">
                        <i class="fas fa-bullhorn"></i>
                        <span>Announcements</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- News Categories -->
    <section class="news-categories section-gap">
        <div class="container">
            <div class="category-filters text-center mb-4">
                <button class="btn btn-filter active" data-category="all">All News</button>
                <button class="btn btn-filter" data-category="academic">Academic</button>
                <button class="btn btn-filter" data-category="event">Events</button>
                <button class="btn btn-filter" data-category="announcement">Announcements</button>
            </div>

            <div class="search-news mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="newsSearch" placeholder="Search news...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>

            <!-- News Updates Section -->
            <div class="row" id="newsContainer">
                <?php
                // Sample news items - Replace with database fetch
                $newsItems = [
                    [
                        'image' => '../images/1.jpg',
                        'title' => 'School Year 2023-2024 Opening',
                        'date' => '2024-01-15',
                        'category' => 'academic',
                        'excerpt' => 'Welcome back students! The new school year begins with excitement and new opportunities.'
                    ],
                    [
                        'image' => '../images/2.jpg',
                        'title' => 'Annual Science Fair 2024',
                        'date' => '2024-02-20',
                        'category' => 'event',
                        'excerpt' => 'Join us for an exciting showcase of student science projects and innovations.'
                    ],
                    [
                        'image' => '../images/3.jpg',
                        'title' => 'Important: Class Schedule Updates',
                        'date' => '2024-02-15',
                        'category' => 'announcement',
                        'excerpt' => 'Please check the revised class schedules for the upcoming semester.'
                    ],
                    [
                        'image' => '../images/4.jpg',
                        'title' => 'New Learning Management System',
                        'date' => '2024-02-10',
                        'category' => 'academic',
                        'excerpt' => 'Introducing our new digital learning platform for enhanced online education.'
                    ],
                    [
                        'image' => '../images/2.jpg',
                        'title' => 'Sports Festival 2024',
                        'date' => '2024-03-01',
                        'category' => 'event',
                        'excerpt' => 'Get ready for our annual sports festival featuring various athletic competitions.'
                    ],
                    [
                        'image' => '../images/1.jpg',
                        'title' => 'Enrollment Period Extended',
                        'date' => '2024-02-25',
                        'category' => 'announcement',
                        'excerpt' => 'The enrollment period has been extended until March 15, 2024.'
                    ]
                ];

                foreach ($newsItems as $news): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card news-card" data-category="<?php echo htmlspecialchars($news['category']); ?>">
                            <div class="card-img-wrapper">
                                <img src="<?php echo htmlspecialchars($news['image']); ?>" class="card-img-top" alt="News Image">
                                <div class="category-badge <?php echo htmlspecialchars($news['category']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($news['category'])); ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="news-meta">
                                    <span class="date">
                                        <i class="far fa-calendar-alt"></i> 
                                        <?php echo date('M d, Y', strtotime($news['date'])); ?>
                                    </span>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($news['excerpt']); ?></p>
                                <a href="news-detail.php?id=<?php echo $news['id'] ?? '1'; ?>" class="btn btn-outline-primary">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="text-center py-5" style="display: none;">
                <i class="fas fa-search fa-3x text-muted"></i>
                <h3 class="mt-3">No Results Found</h3>
                <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="News pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsContainer = document.getElementById('newsContainer');
        const filterButtons = document.querySelectorAll('.btn-filter');
        const searchInput = document.getElementById('newsSearch');
        const noResults = document.getElementById('noResults');

        // Filter functionality
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter news cards
                const newsCards = document.querySelectorAll('.news-card');
                let visibleCards = 0;

                newsCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.closest('.col-lg-4').style.display = 'block';
                        visibleCards++;
                    } else {
                        card.closest('.col-lg-4').style.display = 'none';
                    }
                });

                // Show/hide no results message
                noResults.style.display = visibleCards === 0 ? 'block' : 'none';
            });
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const newsCards = document.querySelectorAll('.news-card');
            let visibleCards = 0;

            newsCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const content = card.querySelector('.card-text').textContent.toLowerCase();
                const category = card.dataset.category.toLowerCase();

                if (title.includes(searchTerm) || content.includes(searchTerm) || category.includes(searchTerm)) {
                    card.closest('.col-lg-4').style.display = 'block';
                    visibleCards++;
                } else {
                    card.closest('.col-lg-4').style.display = 'none';
                }
            });

            // Show/hide no results message
            noResults.style.display = visibleCards === 0 ? 'block' : 'none';
        });
    });
    </script>
</body>
</html>

