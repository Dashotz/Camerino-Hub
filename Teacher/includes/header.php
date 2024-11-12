<header class="dashboard-header">
    <div class="header-left">
        <button id="sidebar-toggle" class="btn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="header-right">
        <div class="notifications">
            <a href="#" class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </a>
        </div>
        
        <div class="messages">
            <a href="#" class="message-icon">
                <i class="fas fa-envelope"></i>
                <span class="badge">5</span>
            </a>
        </div>
        
        <div class="user-profile dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <img src="../images/teacher.png" alt="Profile" class="profile-img">
                <span class="user-name"><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</header>
