<?php

error_reporting(0);
ini_set('display_errors', 0);

class DbConnector {

var $theQuery;
public $link;  // CamerinoHub database connection
public $messageLink;  // Message database connection
private $isConnClosed = false;
private $isMsgConnClosed = false;
private $conn;

// Add these as class properties
// SECURITY: Replace with your actual database credentials
// For production, use environment variables: getenv('DB_HOST'), getenv('DB_NAME'), etc.
private $host = 'localhost';
private $username = 'your_username';
private $password = 'your_password';
private $database = 'your_database_name';
private $messageDatabase = 'your_message_database_name'; // Optional: separate database for messages

function __construct($useMessageDb = false) {
        // Use the class properties instead of local variables
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            throw new Exception('Connection failed: ' . $this->conn->connect_error);
        }

        // Set UTF-8 charset for proper encoding
        $this->conn->set_charset('utf8mb4');

        // If message database is needed, create that connection
        if ($useMessageDb) {
            $this->messageLink = new mysqli($this->host, $this->username, $this->password, $this->messageDatabase);
            if ($this->messageLink->connect_error) {
                throw new Exception('Connection failed: ' . $this->messageLink->connect_error);
            }
            $this->messageLink->set_charset('utf8mb4');
        }

        register_shutdown_function(array(&$this, 'close'));
    }
	
  //*** Function: query, Purpose: Execute a database query ***
    function query($query) {
        $this->theQuery = $query;
        $result = mysqli_query($this->conn, $query);
        if (!$result) {
            error_log("CamerinoHub Query Error: " . mysqli_error($this->conn));
            error_log("Query was: " . $query);
        }
        return $result;
    }

    // Query for message database with error logging
    function messageQuery($query) {
        if (!$this->messageLink) {
            throw new Exception("Message database connection not established");
        }
        $this->theQuery = $query;
        $result = mysqli_query($this->messageLink, $query);
        if (!$result) {
            error_log("Message DB Query Error: " . mysqli_error($this->messageLink));
            error_log("Query was: " . $query);
        }
        return $result;
    }

    //*** Function: fetchArray, Purpose: Get array of query results ***
    function fetchArray($result) {
        return mysqli_fetch_array($result);
    }

    // Get associative array
    function fetchAssoc($result) {
        return mysqli_fetch_assoc($result);
    }

    // Get number of rows
    function numRows($result) {
        return mysqli_num_rows($result);
    }

    // Get last insert ID
    function lastInsertId($useMessageDb = false) {
        return $useMessageDb ? 
            mysqli_insert_id($this->messageLink) : 
            mysqli_insert_id($this->conn);
    }

    //*** Function: close, Purpose: Close the connection ***
    function close() {

        if (!$this->isConnClosed && $this->conn) {
            mysqli_close($this->conn);
            $this->isConnClosed = true;
        }
        if (!$this->isMsgConnClosed && $this->messageLink) {
            mysqli_close($this->messageLink);
            $this->isMsgConnClosed = true;
        }

    }

    // Add new method for safe queries
    function escapeString($string, $useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->conn;
        return mysqli_real_escape_string($conn, $string);
    }

    // Add this new method to your DbConnector class
    function prepare($query, $useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->conn;
        if (!$conn) {
            throw new Exception(($useMessageDb ? "Message" : "Main") . " database connection not established");
        }
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($conn));
            error_log("Query was: " . $query);
        }
        return $stmt;
    }

    // Transaction methods for message database
    function beginTransaction($useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->conn;
        if ($conn) {
            mysqli_begin_transaction($conn);
        }
    }

    function commit($useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->conn;
        if ($conn) {
            mysqli_commit($conn);
        }
    }

    function rollback($useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->conn;
        if ($conn) {
            mysqli_rollback($conn);
        }
    }

    // Helper method for cross-database queries
    function crossDatabaseQuery($query) {
        if (!$this->messageLink) {
            throw new Exception("Message database connection required for cross-database queries");
        }
        return $this->query($query);
    }
	
    // Add this method to handle escaping strings
    function real_escape_string($string) {
        return $this->conn->real_escape_string($string);
    }

    // Add this method to get the connection object
    function getConnection() {
        return $this->conn;
    }

    public function begin_transaction() {
        return $this->conn->begin_transaction();
    }

    public function fetchQuizzes($section_subject_id) {
        $query = "SELECT a.*, 
                  ss.section_id,
                  s.section_name,
                  sub.subject_name,
                  CASE 
                      WHEN a.due_date > NOW() THEN 'upcoming'
                      ELSE 'expired'
                  END as quiz_status
                  FROM activities a
                  JOIN section_subjects ss ON a.section_subject_id = ss.id
                  JOIN sections s ON ss.section_id = s.section_id
                  JOIN subjects sub ON ss.subject_id = sub.id
                  WHERE a.type = 'quiz' 
                  AND a.section_subject_id = ?
                  AND a.status = 'active'
                  ORDER BY a.created_at DESC";
                  
        $stmt = $this->prepare($query);
        $stmt->bind_param("i", $section_subject_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchStudentQuizzes($student_id) {
        $query = "SELECT a.*,
                  ss.section_id,
                  s.section_name,
                  sub.subject_name,
                  CASE 
                      WHEN a.due_date > NOW() THEN 'upcoming'
                      ELSE 'expired'
                  END as quiz_status,
                  sas.submission_id,
                  sas.submitted_at,
                  sas.score
                  FROM activities a
                  JOIN section_subjects ss ON a.section_subject_id = ss.id
                  JOIN sections s ON ss.section_id = s.section_id
                  JOIN subjects sub ON ss.subject_id = sub.id
                  JOIN student_sections sts ON s.section_id = sts.section_id
                  LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
                      AND sas.student_id = ?
                  WHERE a.type = 'quiz'
                  AND sts.student_id = ?
                  AND a.status = 'active'
                  ORDER BY a.due_date DESC";
                  
        $stmt = $this->prepare($query);
        $stmt->bind_param("ii", $student_id, $student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Add these methods to your DbConnector class
    public function getHost() {
        return $this->host;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDatabase() {
        return $this->database;
    }

}

?>
