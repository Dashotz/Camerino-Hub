<?php
require_once('../db/dbConnector.php');

$db = new DbConnector();

// Clean up sessions older than 10 minutes
$cleanup_query = "UPDATE student 
                 SET user_online = 0, 
                     session_id = NULL,
                     last_activity = NULL 
                 WHERE (last_activity IS NULL 
                     OR last_activity < DATE_SUB(NOW(), INTERVAL 10 MINUTE))
                 AND user_online = 1";
$db->query($cleanup_query);

// Double-check and force cleanup for any stuck sessions
$force_cleanup = "UPDATE student 
                 SET user_online = 0, 
                     session_id = NULL,
                     last_activity = NULL 
                 WHERE user_online = 1 
                 AND (session_id IS NULL OR last_activity IS NULL)";
$db->query($force_cleanup);
?>
