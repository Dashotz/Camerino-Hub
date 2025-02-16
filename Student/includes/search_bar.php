<?php
// Create this new file to hold the standardized search bar component
?>
<div class="search-container animate__animated animate__fadeInUp animate__delay-3s">
    <form action="search_results.php" method="GET" class="search-form">
        <div class="input-group">
            <input type="text" 
                   name="query" 
                   id="searchInput" 
                   class="form-control"
                   placeholder="<?php echo $isLoggedIn ? 'Search something...' : 'Search news, updates, and information...'; ?>"
                   <?php echo !$isLoggedIn ? 'readonly' : ''; ?>
                   data-bs-toggle="<?php echo !$isLoggedIn ? 'tooltip' : ''; ?>"
                   data-bs-placement="bottom"
                   title="<?php echo !$isLoggedIn ? 'Login to access advanced search features' : ''; ?>"
            >
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div> 