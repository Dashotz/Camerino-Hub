<?php
// Ensure $isLoggedIn and $userData are available
if (!isset($isLoggedIn)) {
    $isLoggedIn = isset($_SESSION['id']);
}

if (!isset($userData) && $isLoggedIn) {
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

<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="home.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
            <span class="logo-text ml-2">Gov D.M. Camerino</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'home') ? 'active' : ''; ?>" href="home.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'site-map') ? 'active' : ''; ?>" href="site-map.php">
                        <i class="fas fa-sitemap"></i> Site Map
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'news') ? 'active' : ''; ?>" href="News.php">
                        <i class="fas fa-newspaper"></i> News
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'about') ? 'active' : ''; ?>" href="aboutus.php">
                        <i class="fas fa-info-circle"></i> About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage === 'contact') ? 'active' : ''; ?>" href="contactus.php">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                </li>
                
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" 
                           role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($userData['profile_picture'] ?? '../images/default-avatar.png'); ?>" 
                                 alt="Profile" class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <span><?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="student_dashboard.php">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                            <a class="dropdown-item" href="student_profile.php">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary btn-signup text-white px-4" href="Student-Login.php">
                            <i class="fas fa-sign-in-alt mr-2"></i> Log In
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Additional Navigation Styles */
.navbar {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 0.5rem 1rem;
}

.navbar-logo {
    width: 40px;
    height: auto;
}

.logo-text {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color, #1B3C74);
}

.nav-link {
    padding: 0.5rem 1rem !important;
    font-weight: 500;
    transition: color 0.3s ease;
}

.nav-link i {
    margin-right: 5px;
}

.nav-link.active {
    color: var(--primary-color, #1B3C74) !important;
    font-weight: 600;
}

.btn-signup {
    border-radius: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-signup:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    padding: 0.5rem;
}

.dropdown-item {
    border-radius: 4px;
    padding: 0.5rem 1rem;
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background-color: var(--hover-bg, #f8f9fa);
}

@media (max-width: 991.98px) {
    .navbar-nav {
        padding: 1rem 0;
    }
    
    .nav-item {
        margin: 0.25rem 0;
    }
    
    .btn-signup {
        margin-top: 0.5rem;
        width: 100%;
        text-align: center;
    }
}
</style>
