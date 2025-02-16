<?php
session_start();
require_once('../db/dbConnector.php');

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Get user data if logged in
$userData = null;
if ($isLoggedIn) {
    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    $query = "SELECT * FROM student WHERE student_id = '$student_id'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_array($result);
    }
}

// Get search query
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$results = [];

if (!empty($searchQuery)) {
    $db = new DbConnector();
    $searchQuery = $db->real_escape_string($searchQuery);
    
    // Basic search for non-logged in users
    $sql = "SELECT 'news' as type, id, title, excerpt as description, date, category 
            FROM news 
            WHERE status = 'active' 
            AND (title LIKE '%$searchQuery%' OR excerpt LIKE '%$searchQuery%')";
    
    // Additional search areas for logged-in users
    if ($isLoggedIn) {
        $sql .= " UNION 
                SELECT 'about' as type, id, title, content as description, created_at as date, type as category 
                FROM about_us_content 
                WHERE status = 'active' 
                AND (title LIKE '%$searchQuery%' OR content LIKE '%$searchQuery%')";
    }
    
    // Include site map related searches for all users
    $sql .= " UNION 
            SELECT 
            'location' as type,
            name as title, 
            description,
            'facility' as category 
            FROM facilities 
            WHERE status = 'active' 
            AND (name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%')
            
            UNION
            
            SELECT 
            'department' as type,
            department_name as title,
            description,
            'academic' as category
            FROM departments
            WHERE status = 'active'
            AND (department_name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%')";
    
    // Add other search types if user is logged in
    if ($isLoggedIn) {
        $sql .= " UNION 
                SELECT 'news' as type, title, excerpt as description, category 
                FROM news 
                WHERE status = 'active' 
                AND (title LIKE '%$searchQuery%' OR excerpt LIKE '%$searchQuery%')";
    }
    
    $sql .= " ORDER BY date DESC";
    
    $result = $db->query($sql);
    while ($row = $db->fetchAssoc($result)) {
        $results[] = $row;
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
    <link rel="stylesheet" href="css/search.css">
	<link rel="icon" href="../images/light-logo.png">
</head>
<body>
    <!-- Include your navigation here -->
    
    <div class="container mt-5">
        <h2>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
        
        <!-- Search form -->
        <div class="search-container mb-4">
            <form action="search_results.php" method="GET" class="search-form">
                <div class="input-group">
                    <input type="text" 
                           name="query" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($searchQuery); ?>"
                           placeholder="<?php echo $isLoggedIn ? 'Search something...' : 'Search news, updates, and information...'; ?>"
                           <?php echo !$isLoggedIn ? 'readonly' : ''; ?>>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results -->
        <?php if (empty($results)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No results found for "<?php echo htmlspecialchars($searchQuery); ?>"
            </div>
        <?php else: ?>
            <div class="search-results">
                <?php foreach ($results as $result): ?>
                    <div class="result-card">
                        <div class="result-type">
                            <span class="badge badge-<?php echo $result['type'] === 'news' ? 'primary' : 'secondary'; ?>">
                                <?php echo ucfirst($result['type']); ?>
                            </span>
                        </div>
                        <h3>
                            <a href="<?php echo $result['type']; ?>-detail.php?id=<?php echo $result['id']; ?>">
                                <?php echo htmlspecialchars($result['title']); ?>
                            </a>
                        </h3>
                        <p><?php echo htmlspecialchars(substr($result['description'], 0, 200)) . '...'; ?></p>
                        <div class="result-meta">
                            <span><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($result['date'])); ?></span>
                            <span><i class="fas fa-tag"></i> <?php echo ucfirst($result['category']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include your footer here -->
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
