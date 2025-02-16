<?php
// Ensure session is started and variables are set
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Current page handling
if (!isset($currentPage)) {
    $currentPage = '';
}
?>

<!-- Header and Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="student_dashboard.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
            <span class="logo-text">Gov D.M. Camerino</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'home') ? 'active' : ''; ?>" 
                       href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'site-map') ? 'active' : ''; ?>" 
                       href="site-map.php">Site Map</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'news') ? 'active' : ''; ?>" 
                       href="News.php">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'about') ? 'active' : ''; ?>" 
                       href="aboutus.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'contact') ? 'active' : ''; ?>" 
                       href="contactus.php">Contact Us</a>
                </li>
                
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
                    <li class="nav-item">
                        <a class="nav-link btn-signup" href="Student-Login.php">Log In</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>