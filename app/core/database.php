<?php
namespace rbwebdesigns;

/******************************************************************
    Class DB
    - All low level database access using PHP PDO classes
    - Select, update, insert and delete functions
    - Connection to database
    - All SQL errors caught and handled in consistant way
    - Limitations/ future improvements:
        - update and delete only handle WHERE val=this
        - update, insert and delete will only handle one insert
        - data needs to be validated and verified before being
        passed here.
        - could use prepare statements instead?
    R.Bertram 27 NOV 2013
******************************************************************/

class DB {

    private $db_name, $db_user, $db_pass, $db_server;
    private $db_connection;

    /**
        Construct Database class passing in connection credentials
    **/
    public function __construct($psDbServer, $psDbUser, $psDbPass, $psDbName) {
        $this->db_name = $psDbName;
        $this->db_user = $psDbUser;
        $this->db_pass = $psDbPass;
        $this->db_server = $psDbServer;
        $this->db_connection = $this->getConnection();
    }
    
    
    /**
        Connect to database using PDO
    **/
    public function getConnection() {
        try {
            // Try to connect
            $db_connect = new \PDO('mysql:host='.$this->db_server.';dbname='.$this->db_name, $this->db_user, $this->db_pass);
            // Set exceptions to show
            $db_connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        // Catch connection errors
        catch(\PDOException $e) {
            die($this->showSQLError($e));
        }
        return $db_connect;
    }

    
    /**
        Get the ID number of the last inserted row using PDO standard methods
    **/
    public function getLastInsertID() {
        return $this->db_connection->lastInsertId();
    }
    
    
    /**
        Run a standard query straight into the database
        (not really the safest way but sometimes necessary!)
    **/
    public function runQuery($psQueryString) {       
        try
        {
            $query = $this->db_connection->query($psQueryString);
        }
        catch(\PDOException $e) {
            die($this->showSQLError($e, $psQueryString));
        }
		// echo $psQueryString;
		
        return $query;
	}
    
    
    /**
        Format a helpful SQL error message
    **/
    private function showSQLError($err, $psQueryString="") {
		if(IS_DEVELOPMENT) {
			$errMessage = '<p class="error">';
			$errMessage.= '  <strong>Database Error!</strong><br>';
			$errMessage.= '  Details: '.$err->getMessage().'<br>';
			$errMessage.= '  File: <strong>'.$err->getFile().'</strong><br>';
			$errMessage.= '  Line: <strong>'.$err->getLine().'</strong><br>';
			if(strlen($psQueryString) > 0) $errMessage.= 'SQL: <strong>'.$psQueryString.'</strong>';
			$errMessage.= '</p>';
		} else {
			$errMessage = 'Oh No! Something has gone wrong, please contact support regarding a database error!';
		}
        return $errMessage;
    }
    
    
    private function prepareSimpleSelect($psDbTable, $pColumnsToSelect, $parrWhere, $strOrderBy, $strLimit) {
	
		// What
        if(gettype($pColumnsToSelect) == 'string') $lsColumnsToSelect = $pColumnsToSelect;  
        else $lsColumnsToSelect = implode(',', $pColumnsToSelect);
        
		if(gettype($parrWhere) == 'string') $psWhere = ' ' . $parrWhere;
        else $psWhere = $this->createWhereStatement($parrWhere);
        
		// Order
		if(strlen($strOrderBy) > 0) $strOrderBy = ' ORDER BY '.$strOrderBy;
		
		// Limit
		if(strlen($strLimit) > 0) $strLimit = ' LIMIT '.$strLimit;
	
        return 'SELECT '.$lsColumnsToSelect.' FROM '.$psDbTable.' WHERE '.$psWhere.$strOrderBy.$strLimit;
    }
    
    
    public function selectSingleRow($psDbTable, $pColumnsToSelect, $parrWhere, $strOrderBy='', $strLimit='') {
        $lsQueryString = $this->prepareSimpleSelect($psDbTable, $pColumnsToSelect, $parrWhere, $strOrderBy, $strLimit);
        // echo $lsQueryString;
		$query = $this->runQuery($lsQueryString);
        return $query->fetch(\PDO::FETCH_ASSOC);
    }
    public function selectMultipleRows($psDbTable, $pColumnsToSelect, $parrWhere, $strOrderBy='', $strLimit='') {
        $lsQueryString = $this->prepareSimpleSelect($psDbTable, $pColumnsToSelect, $parrWhere, $strOrderBy, $strLimit);
		$query = $this->runQuery($lsQueryString);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
	
    
	public function countRows($psDbTable, $parrWhere='') {
        if(getType($parrWhere) == 'array') {
            $querystring = $this->prepareSimpleSelect($psDbTable, 'count(*) as rowcount', $parrWhere, '', '');
        } else {
            $querystring = 'SELECT count(*) as rowcount from '.$psDbTable;
        }
		$query = $this->runQuery($querystring);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
		return $result['rowcount'];
	}
	
    
	// Perform a SELECT query that is expected to return ONE result
    public function select_single($querystring) {
    
        try {
            $query = $this->db_connection->query($querystring);
        
        } catch(PDOException $e) { die(showQueryError($e, $querystring)); }
        
        return $query->fetch(\PDO::FETCH_ASSOC);
    }
	
    // Perform a SELECT query that will contain 0 or more results
    public function select_multi($querystring) {
        try {
            $query = $this->db_connection->query($querystring);
        
        } catch(PDOException $e) { die(showQueryError($e, $querystring)); }
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
	
    
    /*
        ISSUE! Cannot have multiple limitations on the same field as array(
            'timestamp' => '>now'
            'timestamp' => '<later'
        ) will only keep the second rule!
    */
    private function createWhereStatement($parrWhere) {
        $i = 0;
        $lsWhere = '';
        foreach($parrWhere as $key => $value):
        
        // echo "key=".$key." value=".$value;
        
            if($value[0] == '>') {
                $value = ltrim($value, '>');
                $op = '>';
                
            } else if($value[0] == '<') {
                $value = ltrim($value, '<');
                $op = '<';
            } else {
                $op = '=';
            }
            
            // Look for dates
            if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $value)) {
                $value = 'TIMESTAMP("'.$value.'")';
            }
            elseif(trim($value) == 'CURRENT_TIMESTAMP') {
                 $value = "CURRENT_TIMESTAMP"; // hack around for sql keyword.
            }
            else {
                $value = "'".$value."'";
            }
            
            $comma = ($i == 0) ? '': ' AND '; $i++;
            $lsWhere.= $comma.' '.$key.$op.$value;
        endforeach;
        return $lsWhere;
    }
    
    /**
        Insert a new row into the database
    **/
    public function insertRow($psDbTable, $parrValues) {
        $i = $j = 0;
        $lsColumnNames = $lsValues = '';
        foreach($parrValues as $key => $value):
            $comma = ($i == 0) ? '': ','; $i++;
            $lsColumnNames.= $comma.'`'.$key.'`';
            $lsValues.= $comma.'"'.$value.'"';
        endforeach;
        $lsQueryString = 'INSERT INTO '.$psDbTable.' ('.$lsColumnNames.') VALUES ('.$lsValues.')';
        return $this->runQuery($lsQueryString);
    }
    
    /**
        Update a (single) row into the database
    **/
    public function updateRow($psDbTable, $parrWhere, $parrValues) {
        $i = 0;
        $lsColumnNames = $lsWhere = '';
        foreach($parrValues as $key => $value):
            $comma = ($i == 0) ? '': ',';
            $lsColumnNames.= $comma.' `'.$key.'`="'.$value.'"';
			$i++;
        endforeach;
		$lsWhere = $this->createWhereStatement($parrWhere);
        $lsQueryString = 'UPDATE '.$psDbTable.' SET '.$lsColumnNames.' WHERE '.$lsWhere;		
        return $this->runQuery($lsQueryString);
    }
    
    /**
        Delete a row into the database
    **/
    public function deleteRow($psDbTable, $parrWhere) {
        $lsWhere = $this->createWhereStatement($parrWhere);
        $lsQueryString = 'DELETE FROM '.$psDbTable.' WHERE '.$lsWhere;
        return $this->runQuery($lsQueryString);
    }
}
?>