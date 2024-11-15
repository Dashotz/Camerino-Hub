<?php
header('Content-Type: application/json');
require_once('../db/dbConnector.php');

$suggestions = [];

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $db = new DbConnector();
    $query = $db->real_escape_string($_GET['query']);
    
    // Get suggestions from different tables
    $suggestionsQuery = "
        SELECT DISTINCT title 
        FROM (
            SELECT title FROM news WHERE title LIKE '%$query%'
            UNION
            SELECT title FROM about_us_content WHERE title LIKE '%$query%'
            UNION
            SELECT title FROM contact_information WHERE title LIKE '%$query%'
        ) AS combined_results
        LIMIT 5
    ";
    
    $result = $db->query($suggestionsQuery);
    
    if ($result) {
        while ($row = $db->fetchAssoc($result)) {
            $suggestions[] = $row['title'];
        }
    }
}

echo json_encode($suggestions); 