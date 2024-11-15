<?php
session_start();
require_once('../db/dbConnector.php');

// Initialize search results array
$searchResults = [];

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $db = new DbConnector();
    $query = $db->real_escape_string($_GET['query']);
    
    // Search in News
    $newsQuery = "SELECT 
        'news' as type,
        title,
        content,
        created_at,
        'news.php' as link 
    FROM news 
    WHERE (title LIKE '%$query%' OR content LIKE '%$query%')
    AND status = 'active'";
    
    // Search in About Us
    $aboutUsQuery = "SELECT 
        'about' as type,
        title,
        content,
        created_at,
        'aboutus.php' as link 
    FROM about_us_content 
    WHERE (title LIKE '%$query%' OR content LIKE '%$query%')
    AND status = 'active'";
    
    // Search in Contact Us
    $contactQuery = "SELECT 
        'contact' as type,
        title,
        content,
        created_at,
        'contactus.php' as link 
    FROM contact_information 
    WHERE (title LIKE '%$query%' OR content LIKE '%$query%')
    AND status = 'active'";
    
    // Search in Site Map
    $siteMapQuery = "SELECT 
        'sitemap' as type,
        'Site Map' as title,
        description as content,
        created_at,
        'site-map.php' as link 
    FROM site_map_content 
    WHERE description LIKE '%$query%'
    AND status = 'active'";
    
    // Combine all searches
    $unionQuery = "$newsQuery 
                  UNION 
                  $aboutUsQuery 
                  UNION 
                  $contactQuery 
                  UNION 
                  $siteMapQuery 
                  ORDER BY created_at DESC";
                  
    $result = $db->query($unionQuery);
    
    if ($result) {
        while ($row = $db->fetchAssoc($result)) {
            $searchResults[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/search.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="mb-3">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="search-header">
            <h2><i class="fas fa-search"></i> Search Results</h2>
            <p>Showing results for: "<span class="search-term"><?php echo htmlspecialchars($_GET['query'] ?? ''); ?></span>"</p>
        </div>
        
        <?php if (empty($searchResults)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No results found. Try different keywords or browse our pages:
                <div class="mt-3">
                    <a href="news.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-newspaper"></i> News
                    </a>
                    <a href="aboutus.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-info-circle"></i> About Us
                    </a>
                    <a href="contactus.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                    <a href="site-map.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-sitemap"></i> Site Map
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="search-results">
                <?php foreach ($searchResults as $result): ?>
                    <div class="result-card">
                        <div class="result-type <?php echo $result['type']; ?>">
                            <?php 
                            $icon = '';
                            switch($result['type']) {
                                case 'news': $icon = 'newspaper'; break;
                                case 'about': $icon = 'info-circle'; break;
                                case 'contact': $icon = 'envelope'; break;
                                case 'sitemap': $icon = 'sitemap'; break;
                            }
                            ?>
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                            <?php echo ucfirst($result['type']); ?>
                        </div>
                        <h3>
                            <a href="<?php echo htmlspecialchars($result['link']); ?>">
                                <?php echo htmlspecialchars($result['title']); ?>
                            </a>
                        </h3>
                        <p><?php echo substr(htmlspecialchars($result['content']), 0, 200) . '...'; ?></p>
                        <div class="result-meta">
                            <span class="result-date">
                                <i class="far fa-clock"></i>
                                <?php echo date('M d, Y', strtotime($result['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="floating-back-button">
        <a href="javascript:history.back()" class="btn btn-primary rounded-circle">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
