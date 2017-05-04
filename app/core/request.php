<?php
namespace rbwebdesigns;
/**********************************************************
    core/request.php
    
    Static Functions
        Request::GetStringVariable('name', 'GET')
        Request::GetNumberVariable('name', 'GET')
    
**********************************************************/

class Request {

    /**
        Get the value of a request variable expected as a string
        @param <string> type (optional) - GET / POST
        @return <string> value of request variable if found - null otherwise
    **/
    public static function GetStringVariable($key, $type='GET') {
        if(strtoupper($type) === 'GET') {
            return (isset($_GET['s'])) ? safeString($_GET['s']) : null;
        } else {
            return (isset($_POST['s'])) ? safeString($_POST['s']) : null;
        }
    }
    
    
    /**
        Get the value of a request variable expected as a number
        @param <string> key - Name of request key
        @param <string> type (optional) - GET / POST
        @return <int> value of request variable if found - null otherwise
    **/
    public static function GetNumberVariable($key, $type='GET') {
        if(strtoupper($type) === 'GET') {
            return isset($_GET['s']) ? safeNumber($_GET['s']) : null;
        } else {
            return isset($_POST['s']) ? safeNumber($_POST['s']) : null;
        }
    }
}

?>