<?php

class DbConnector {

var $theQuery;
public $link;  // Changed to public
private $isConnClosed = false;

function __construct(){  // Updated to use modern constructor
        // Get the main settings from the array we just loaded
        $host = 'localhost';
        $db = 'elearning';
        $user = 'root';
        $pass = '';

        // Connect to the database
        $this->link = mysqli_connect($host, $user, $pass, $db);
        if (!$this->link) {
            die("Connection failed: " . mysqli_connect_error());
        }
        register_shutdown_function(array(&$this, 'close'));
    }
	
  //*** Function: query, Purpose: Execute a database query ***
    function query($query) {

        $this->theQuery = $query;
        return mysqli_query($this->link,$query);

    }

    //*** Function: fetchArray, Purpose: Get array of query results ***
    function fetchArray($result) {

        return mysqli_fetch_array($result);

    }

    //*** Function: close, Purpose: Close the connection ***
    function close() {

        if (!$this->isConnClosed && $this->link) {
            mysqli_close($this->link);
            $this->isConnClosed = true;
        }

    }
	
}

?>
