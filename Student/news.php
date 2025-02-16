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

require_once('../db/dbConnector.php');

// Initialize database connection
$db = new DbConnector();

// Get search query if exists
$searchQuery = isset($_GET['query']) ? $db->real_escape_string($_GET['query']) : '';

// Fetch news from database with optional search
$sql = "SELECT * FROM news WHERE status = 'active'";
if (!empty($searchQuery)) {
    $sql .= " AND (title LIKE '%$searchQuery%' OR excerpt LIKE '%$searchQuery%')";
}
$sql .= " ORDER BY date DESC";

$result = $db->query($sql);
$newsItems = [];
while ($row = $db->fetchAssoc($result)) {
    $newsItems[] = $row;
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
    <link rel="stylesheet" href="css/shared.css">
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
                <div class="hero-text">
                    <h1>News & Updates<br><span class="highlight">Gov D.M. Camerino</span></h1>
                    <p class="lead">Stay informed with the latest news, events, and announcements</p>
                    <div class="cta-buttons">
                        <?php if ($isLoggedIn): ?>
                            <a href="student_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <?php else: ?>
                            <a href="../login.php" class="btn btn-primary">Login Now</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="../images/student.png" alt="Students">
                </div>
            </div>
            
            <!-- Search Box (Moved here) -->
          
			
            <!-- Quick Links Section -->
            <div class="quick-links animate__animated animate__fadeInUp animate__delay-3s">
                <p>You may be looking for</p>
                <div class="links">
                    <a href="home.php" class="link-item">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="site-map.php" class="link-item">
                        <i class="fas fa-map"></i>
                        <span>Site Map</span>
                    </a>
                    <a href="news.php" class="link-item active">
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

    <!-- News Categories -->
    <section class="news-categories section-gap">
        <div class="container">
            <div class="category-filters text-center mb-4">
                <?php
                // Get active category from URL parameter
                $activeCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
                ?>
                <button class="btn btn-filter <?php echo $activeCategory === 'all' ? 'active' : ''; ?>" 
                        data-category="all">All News</button>
                <button class="btn btn-filter <?php echo $activeCategory === 'academic' ? 'active' : ''; ?>" 
                        data-category="academic">Academic</button>
                <button class="btn btn-filter <?php echo $activeCategory === 'event' ? 'active' : ''; ?>" 
                        data-category="event">Events</button>
                <button class="btn btn-filter <?php echo $activeCategory === 'announcement' ? 'active' : ''; ?>" 
                        data-category="announcement">Announcements</button>
            </div>

            <!-- Search Section -->
            <?php if ($isLoggedIn): ?>
                <div class="search-news mb-4">
                    <form action="news.php" method="GET" class="search-form">
                        <div class="input-group">
                            <input 
                                type="text" 
                                class="form-control" 
                                name="query" 
                                id="newsSearch" 
                                placeholder="Search news..."
                                value="<?php echo htmlspecialchars($searchQuery); ?>"
                            >
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="search-news mb-4 animate__animated animate__fadeInUp animate__delay-3s">
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Please login to access search features" 
                        disabled
                    >
                </div>
            <?php endif; ?>

            <!-- News Updates Section -->
            <div class="row" id="newsContainer">
                <?php if (empty($newsItems)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h3>No News Found</h3>
                        <p class="text-muted">There are no news items to display at this time.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($newsItems as $news): ?>
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
                                    <a href="news-detail.php?id=<?php echo $news['id']; ?>" 
                                       class="btn btn-outline-primary read-more-btn"
                                       onclick="return checkLoginStatus(<?php echo $isLoggedIn ? 'true' : 'false'; ?>)">
                                        Read More <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination (you'll need to implement the logic) -->
            <?php
            $totalPages = ceil(count($newsItems) / 6); // 6 items per page
            if ($totalPages > 1):
            ?>
            <nav aria-label="News pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Add pagination logic here -->
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once('includes/footer.php'); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
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

    <!-- Add this JavaScript for live search functionality -->
    <script>
    document.getElementById('newsSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const newsCards = document.querySelectorAll('.news-card');
        let hasResults = false;

        newsCards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const excerpt = card.querySelector('.card-text').textContent.toLowerCase();
            const category = card.dataset.category.toLowerCase();

            if (title.includes(searchTerm) || 
                excerpt.includes(searchTerm) || 
                category.includes(searchTerm)) {
                card.closest('.col-lg-4').style.display = 'block';
                hasResults = true;
            } else {
                card.closest('.col-lg-4').style.display = 'none';
            }
        });

        // Show/hide no results message
        document.getElementById('noResults').style.display = hasResults ? 'none' : 'block';
    });
    </script>

    <script>
    function checkLoginStatus(isLoggedIn) {
        if (!isLoggedIn) {
            Swal.fire({
                title: 'Login Required',
                text: 'Please login to read the full news article',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Login',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'Student-Login.php';
                }
            });
            return false;
        }
        return true;
    }
    </script>

    <!-- Add this JavaScript at the bottom of your file -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.btn-filter');
        const newsCards = document.querySelectorAll('.news-card');
        const noResults = document.getElementById('noResults');

        // Filter function
        function filterNews(category) {
            let visibleCount = 0;

            newsCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'all' || cardCategory === category) {
                    card.closest('.col-lg-4').style.display = 'block';
                    visibleCount++;
                    // Add fade-in animation
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.opacity = '1';
                    }, 50);
                } else {
                    card.closest('.col-lg-4').style.display = 'none';
                }
            });

            // Show/hide no results message
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('category', category);
            window.history.pushState({}, '', url);
        }

        // Event listeners for filter buttons
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                // Filter news
                filterNews(this.getAttribute('data-category'));
            });
        });
    });
    </script>
</body>
</html>

