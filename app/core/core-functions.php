<?php
/******************************************************************
  core-functions.php
  - Core system functions will be in here
  - Validation and sanitize functions
******************************************************************/

// Show string as an error message
function showError($str) {
	return '<p class="error">'. $str .'</p>';
}

// Show string as an information box
function showInfo($str) {
	return "<p class='info'>". $str ."</p>";
}

// Show a warning box
function showWarning($str) {
	return "<p class='warning'>". $str ."</p>";
}

// Show a warning box
function showSuccess($str) {
	return "<p class='success'>". $str ."</p>";
}

// Show a MySQL Related Specific Error
function showQueryError($err) {
	return "<p class='error'><strong>Database Error!</strong> ".$err->getMessage()." in file <strong>".$err->getFile()."</stong> on line <strong>".$err->getLine()."</strong></p>";
}

// Print a nicely formatted array
function printArray($pArray) {
    return "<pre>".print_r($pArray,true)."</pre>";
}

function setSystemMessage($message, $ptype = "Info") {
	$fn = "show".$ptype;
	$_SESSION['messagetoshow'] = $fn(safeString($message));
	session_write_close(); // Fix for session var not being saved
}
function redirect($location) {
	header('Location: '.$location);
}

/*****************************************************************
  Import CSS and Javascript from array
*****************************************************************/

function importJavascript($javascripts) {
    // Add links to all named js script files
	// if(!defined('SERVER_ROOT')) define('SERVER_ROOT', dirname(__FILE__).'/..');
	// if(!defined('CLIENT_ROOT')) define('SERVER_ROOT', '../');
	
	$warnings = "";
    foreach($javascripts as $filename) {
        // if(file_exists(SERVER_ROOT.$filename.'.js')) {
            echo '<script type="text/javascript" src="'.$filename.'.js"></script>'."\n";
        // } else {
		// 	$warnings.= showInfo('Warning: File '.$filename.'.js has not been found');
		// }
    }
	return $warnings;
}

function importStylesheets($stylesheets) {
	$warnings = "";
	
	// Add links to all named stylesheets
	foreach($stylesheets as $filename):
		// if(file_exists($filename.'.css')) {
			echo '<link type="text/css" rel="stylesheet" href="'.$filename.'.css" />'."\n";
		// } elseif(ISDEV) {
		// 	$warnings.= showInfo('Warning: File '.$filename.'.css has not been found');
		// }
	endforeach;
	return $warnings;
}

/*****************************************************************
  Validation and Sanitization
*****************************************************************/

// Create a wiki-safe string
function sanitizeWikiMarkup($str) {
	$str = safeString($str);
	return $str;
}

// Sample3 forum
function check_input($value) {
	$res = trim($value);
	$res = fix_tags($res);
	$res = strip_tags($res);
	$res = safeString($res);
	return $res;
}

/*****************************************************************
    Functions to convert database query results set to an array
*****************************************************************/

function convertToArray($query_res) {
	$res = array();
	if( $query_res->rowCount() >= 1 ) {
		$res = $query_res->fetch();
	}
	return $res;
}
function convertToMDArray($query_res) {
	$res = array();
	$i = 0;
	
	if( $query_res->rowCount() >= 1 ) {
		while( $item = $query_res->fetch() ) {
			foreach($item as $key => $value) {
				$res[$i][$key] = $value;
			}
			$i++;
		}
	}
	else {}
	return $res;
}

/***********************************************************************
 * function super_unique removes duplicates from a multidimentional array
 * see http://www.php.net/manual/en/function.array-unique.php#97285 for more
 ***********************************************************************/
 
function super_unique($array) {
	$result = array_map("unserialize", array_unique(array_map("serialize", $array)));
	foreach ($result as $key => $value) {
		if ( is_array($value) ) $result[$key] = super_unique($value);
	}
	return $result;
}

/***********************************************************************
    Sample3 Forum HTML Conversion - In future use regexp?
***********************************************************************/

function fix_tags($value) {
	$bb_codes =  array("[b]","[/b]","[strike]","[/strike]","[br]","[span","[/span]","[div","[/div]","[a","[/a]","[ol]","[/ol]","[li]","[/li]","[ul]","[/ul]","[hr]","[img","[table","[/table]","[tr]","[/tr]","[td]","[/td]","[tbody]","[/tbody]");
	$safe_tags = array("<b>","</b>","<strike>","</strike>","<br>","<span","</span>","<div","</div>","<a","</a>","<ol>","</ol>","<li>","</li>","<ul>","</ul>","<hr>","<img","<table","</table>","<tr>","</tr>","<td>","</td>","<tbody>","</tbody>");
	$res = $value;
	$elements = count($bb_codes);
	
	for($i = 0; $i < $elements; $i++) {
		$res = str_replace($safe_tags[$i],$bb_codes[$i],$res);
	}
	
	return $res;
}

function safe_html($value) {
	$bb_codes =  array("[b]","[/b]","[strike]","[/strike]","[br]","[span","[/span]","[div","[/div]","[a","[/a]","[ol]","[/ol]","[li]","[/li]","[ul]","[/ul]","[hr]","[img","[table","[/table]","[tr]","[/tr]","[td]","[/td]","[tbody]","[/tbody]");
	$safe_tags = array("<b>","</b>","<strike>","</strike>","<br>","<span","</span>","<div","</div>","<a","</a>","<ol>","</ol>","<li>","</li>","<ul>","</ul>","<hr>","<img","<table","</table>","<tr>","</tr>","<td>","</td>","<tbody>","</tbody>");
	$res = $value;
	$elements = count($bb_codes);
	
	for($i = 0; $i < $elements; $i++) {
		$res = str_replace($bb_codes[$i],$safe_tags[$i],$res);
	}
	
	return $res;
}


function generateRandomAlphaNumeric($n=8) {

    $characters = array(
    "A","B","C","D","E","F","G","H","J","K","L","M",
    "N","P","Q","R","S","T","U","V","W","X","Y","Z",
    "1","2","3","4","5","6","7","8","9");

    $res = "";

    while(strlen($res) < $n) {
        $res .= $characters[rand(0, 32)];
    }

    return $res;
}

function formatdate($date, $format = "D, jS F Y") {
    return date($format,strtotime($date));
}
function formattime($time, $format = "g:ia") {
    return date($format,strtotime($time));
}


// Opens and converts a json file into an array
function jsonToArray($jsonFileURL) {
	$json_string = file_get_contents($jsonFileURL);
	return json_decode($json_string, true);
}


function sksort(&$array, $subkey="id", $sort_ascending=false) {

	// if the array is more than 1 then add the first element to the result
    if(count($array)) $temp_array[key($array)] = array_shift($array);
	else return false;
	
    foreach($array as $key => $val) {
	
        $offset = 0;
        $found = false;
		
        // if(array_key_exists($subkey, $val)) {
                
        foreach($temp_array as $tmp_key => $tmp_val) {
		        
			// not already found and has a key that is larger than one already in the list
            if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                $temp_array = array_merge(
					(array)array_slice($temp_array, 0, $offset),
                    array($key => $val),
                    array_slice($temp_array, $offset)
                );
                $found = true;
            }
            			
            $offset++;
        }
		
		// this array has the lowest target key
        if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
    }

	// Flip array if need to sort other way round
    if ($sort_ascending) $array = array_reverse($temp_array);
    else $array = $temp_array;
}
?>