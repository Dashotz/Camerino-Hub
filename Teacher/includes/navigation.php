<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="home.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo" height="40">
            <span class="logo-text ml-2">CamerinoHub</span>
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Search bar -->
            <form class="form-inline mx-auto d-none d-md-flex position-relative">
                <input class="form-control search-input" type="search" placeholder="Search...">
                <i class="fas fa-search search-icon"></i>
            </form>

            <!-- Right-aligned items -->
            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Notifications -->
                <li class="nav-item dropdown mx-2">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge badge-danger">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notifications-dropdown">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <div class="notification-item">
                                <i class="fas fa-file-alt text-primary"></i>
                                <div class="notification-content">
                                    <p class="mb-1">New assignment posted</p>
                                    <small class="text-muted">5 minutes ago</small>
                                </div>
                            </div>
                        </a>
                        <!-- More notification items -->
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" 
                       data-toggle="dropdown">
                        <img src="<?php echo $userData['profile_picture'] ?? '../images/teacher.png'; ?>" 
                             alt="Profile" class="profile-img">
                        <span class="d-none d-md-inline ml-2">
                            <?php echo htmlspecialchars($userData['firstname'] ?? 'Teacher'); ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="student_profile.php">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </a>
                        <a class="dropdown-item" href="student_settings.php">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
