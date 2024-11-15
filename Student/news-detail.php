<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Initialize database connection
require_once('../db/dbConnector.php');
$db = new DbConnector();

// Get news ID from URL
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get user data if logged in
$userData = null;
if ($isLoggedIn) {
    $student_id = $_SESSION['id'];
    $query = "SELECT * FROM student WHERE student_id = '$student_id'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_array($result);
    }
}

// Fetch news details
$sql = "SELECT * FROM news WHERE id = ? AND status = 'active'";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

// If news not found, redirect to news listing
if (!$news) {
    header("Location: news.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news['title']); ?> - Gov D.M. Camerino</title>
    <!-- Include your CSS files -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/news.css">
</head>
<body>
    <!-- Full Navigation Code -->
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

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="news.php">News</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($news['title']); ?></li>
                    </ol>
                </nav>

                <article class="news-detail">
                    <div class="news-header">
                        <div class="category-badge <?php echo htmlspecialchars($news['category']); ?>">
                            <?php echo ucfirst(htmlspecialchars($news['category'])); ?>
                        </div>
                        <h1 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h1>
                        <div class="news-meta">
                            <span class="date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('F d, Y', strtotime($news['date'])); ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($news['image']): ?>
                        <div class="news-image">
                            <img src="<?php echo htmlspecialchars($news['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($news['title']); ?>"
                                 class="img-fluid">
                        </div>
                    <?php endif; ?>

                    <div class="news-content">
                        <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                    </div>

                    <div class="news-footer mt-4">
                        <a href="news.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Back to News
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <!-- Add spacing section -->
    <div class="section-gap"></div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 