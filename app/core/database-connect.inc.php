<?php
/*******************************************************************************
    database-connect.inc.php
    @description connection to the database will be established here
	@author R Bertram
	@date 26 DEC 2012

********************************************************************************/

class db {

    private $server;
    private $user;
    private $pass;
    private $dbname;
	private $dbconn;
    
    public function __construct($pserver, $puser, $ppass, $pdbname) {
        $this->server = $pserver;
        $this->user = $puser;
        $this->pass = $ppass;
        $this->dbname = $pdbname;
	    $this->dbconn = $this->connect();
    }

    // Connect to a database using PDO
    public function connect() {
    
        try {
            // Try to connect
            $conn1 = new PDO('mysql:host='.$this->server.';dbname='.$this->dbname, $this->user, $this->pass);
            
            // Set exceptions to show
            $conn1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e) {
        
            // Catch connection errors
            echo 'ERROR: '.$e->getMessage();
        }
        
        return $conn1;
    }
    
    // Get the ID number of the last inserted row
    public function getLastInsertID() {
        return $this->dbconn->lastInsertId();
    }
    
	// Perform a SELECT query that is expected to return ONE result
    public function select_single($querystring) {
    
        try {
            $query = $this->dbconn->query($querystring);
        
        } catch(PDOException $e) { die(showQueryError($e)); }
        
        return $query->fetch(PDO::FETCH_ASSOC);
    }
	
    // Perform a SELECT query that will contain 0 or more results
    public function select_multi($querystring) {
        try {
            $query = $this->dbconn->query($querystring);
        
        } catch(PDOException $e) { die(showQueryError($e)); }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
	
	public function runQuery($querystring) {
        try {
            $query = $this->dbconn->query($querystring);
			return true;
			
        } catch(PDOException $e) { die(showQueryError($e)); }
	}
    
    /**
       values array is a pairing column=>value
    **/
    public function insert_single($tableName, $valuesArray) {
        try {
            $querystring = 'INSERT INTO '.$tableName.' (';
            
            // Column Names
            $i = 0;
            foreach($valuesArray as $key => $value) {
                $comma = ",";
                if($i == 0) {
                    $comma = ""; // don't want a comma for the first run through
                    $i = 1;
                }
                $querystring .= $comma.$key;
            }
            
            $querystring .= ') VALUES (';
            
            // Values
            $i = 0;
            foreach($valuesArray as $value) {
                $comma = ",";
                if($i == 0) {
                    $comma = ""; // don't want a comma for the first run through
                    $i = 1;
                }
                $querystring .= $comma.'"'.$value.'"';
            }
            
            $querystring .= ')';
            
            $go = $this->dbconn->query($querystring);
                               
        } catch(PDOException $e) { die(showQueryError($e)); }
        
        return true;
    }
    /* didn't like the name! - deprecated to runQuery() */
    public function noreturn_query($querystring) {
        try {
            $query = $this->dbconn->query($querystring);
        
        } catch(PDOException $e) { die(showQueryError($e)); }
        
        return true;
    }
    
    // Old Way!!!
    public function connectMYSQL() {
        
    	// Connect to database
        $dbconn1 = mysql_connect($this->server, $this->user, $this->pass);
        
        if(!$dbconn1) {
            die('Could not connect to RBwebdesigns Database - '.mysql_error());
        }

        // Select Table
        $db_selected = mysql_select_db($this->dbname,$dbconn1);
        
        if(!$db_selected){
            die('Could not select database - '.mysql_error());
        }
        
        return $dbconn1;
    }
}
?>