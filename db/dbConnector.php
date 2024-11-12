<?php

class DbConnector {

var $theQuery;
public $link;  // CamerinoHub database connection
public $messageLink;  // Message database connection
private $isConnClosed = false;
private $isMsgConnClosed = false;

function __construct($useMessageDb = false) {
        // Database settings
        $host = 'localhost';
        $mainDb = 'camerinohub';
        $messageDb = 'camerinohub_messages';
        $user = 'root';
        $pass = '';

        // Connect to main CamerinoHub database
        $this->link = mysqli_connect($host, $user, $pass, $mainDb);
        if (!$this->link) {
            die("CamerinoHub DB Connection failed: " . mysqli_connect_error());
        }

        // Set UTF-8 charset for proper encoding
        mysqli_set_charset($this->link, "utf8mb4");

        // If message database is needed, create that connection
        if ($useMessageDb) {
            $this->messageLink = mysqli_connect($host, $user, $pass, $messageDb);
            if (!$this->messageLink) {
                die("CamerinoHub Messages DB Connection failed: " . mysqli_connect_error());
            }
            mysqli_set_charset($this->messageLink, "utf8mb4");
        }

        register_shutdown_function(array(&$this, 'close'));
    }
	
  //*** Function: query, Purpose: Execute a database query ***
    function query($query) {
        $this->theQuery = $query;
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            error_log("CamerinoHub Query Error: " . mysqli_error($this->link));
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
            mysqli_insert_id($this->link);
    }

    //*** Function: close, Purpose: Close the connection ***
    function close() {

        if (!$this->isConnClosed && $this->link) {
            mysqli_close($this->link);
            $this->isConnClosed = true;
        }
        if (!$this->isMsgConnClosed && $this->messageLink) {
            mysqli_close($this->messageLink);
            $this->isMsgConnClosed = true;
        }

    }

    // Add new method for safe queries
    function escapeString($string, $useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->link;
        return mysqli_real_escape_string($conn, $string);
    }

    // Add this new method to your DbConnector class
    function prepare($query, $useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->link;
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
        $conn = $useMessageDb ? $this->messageLink : $this->link;
        if ($conn) {
            mysqli_begin_transaction($conn);
        }
    }

    function commit($useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->link;
        if ($conn) {
            mysqli_commit($conn);
        }
    }

    function rollback($useMessageDb = false) {
        $conn = $useMessageDb ? $this->messageLink : $this->link;
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
	
}

?>
