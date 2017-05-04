<?php
/*
	$_GET['s'] = page number
*/
class pagination {


    public function __construct() {
	
	}

	public function getCurrentFullURL() {
		// set the path of links as this page will be accessed from multiple places
		$url = $_SERVER['PHP_SELF']."?";
		$hiddenfields = "";
		$x = false;
		// add get variables to path of links as they are important!
		foreach($_GET as $key => $value) {
		 if($key == 's') { 
			// don't copy current page
		 } else {
			$url .= $x ? "&" : "";
			$url .= safeString($key)."=".safeString($value);
			$x = true;
		  }
		}
		return $url;
	}

	public function showPagination($pages,$pagenum) {
		$res = '<div class="pagination">';
		
		$url = getCurrentFullURL();
		
		// Don't show back link if current page is first page.
		if ($pagenum != 1) {
			$res .= '<a href="'.$url.'&s='.($pagenum-1).'">&lt;</a>';
		}
		// loop through each page and give link to it.
		for ($i = 1; $i <= $pages; $i++) {
			if ($pagenum == $i) $res .= '<a><b>'.$i.'</b></a>';
			else $res .= '<a href="'.$url.'&s='.$i.'">'.$i.'</a>';
		}
		// If last page don't give next link.
		if ( $pagenum < $pages ) {
			$res .= '<a href="'.$url.'&s='.($pagenum+1).'">&gt;</a>';
		}
		$res .= '</div>';
		
		return $res;
	}
    
    
    /* New - a little bit untidy at the moment! */
    
    function setupPagination_2($model, $table, $what, $where) {
    
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 5;
        
        $sortby = isset($_POST['sortby']) ? $_POST['sortby'] : null;
        $sortasc = isset($_POST['sortdir']) ? $_POST['sortdir'] : 'asc';
        $sortasc = ($sortasc == 'asc') ? 'asc' : 'desc'; // can only be these two values!
        
        // Count all rows (example)
        $totalrows = $model->getCount($table, $where);
        $totalpages = ceil($totalrows / $limit);
        $startpoint = ($page-1) * $limit;
        
        $validsortby = '';
        if($sortby != null) {
            $arrayFields = $model->getFields();
            if(array_key_exists($sortby, $arrayFields)) {
                $validsortby = $sortby.' '.$sortasc;
            }
        }
        
        $data = $model->get($what, array('gender'=>'male'), $validsortby, $startpoint.','.$limit);
        
        return array(
            'data' => $data,
            'page' => $page,
            'limit' => $limit,
            'sortby' => $sortby,
            'sortdir' => $sortasc,
            'rowcount' => $totalrows,
            'pagecount' => $totalpages,
            'startpoint' => $startpoint
        );
    }
    
    function viewTable($config, $fn=null, $columnHeadings='') {    
        
        /* requires setupPagination_2 to be run first
        this function then has extra options for custom display function and column headings
        */
        $totalrows = $config['rowcount'];
        $totalpages = $config['pagecount'];
        $page = $config['page'];
        $limit = $config['limit'];
        $data = $config['data'];
        
        ////// Add header //////
        // can be passed in as CSV or array
        echo '<thead><tr><th colspan="999" style="text-align:left;">'.$totalrows.' Results - Page '.$page.' of '.$totalpages.'</th></tr><tr>';
        if(getType($columnHeadings) == 'string' && strlen($columnHeadings) > 0) {
            $columnHeadings = explode(',', $columnHeadings); // split by comma
        }
        if(getType($columnHeadings) == 'array') {
            if(count($data) > 0 && getType($data[0]) == 'array') {
                $datakeys = array_keys($data[0]);
            } else {
                $datakeys = $columnHeadings; // this might not work but just do something!!
            }
            $current = 0;
            foreach($columnHeadings as $column) {
                echo '<th>'.$column.' <a href="#" onclick="sortdata(\''.$datakeys[$current].'\',\'asc\')">&#9650;</a><a href="#" onclick="sortdata(\''.$datakeys[$current].'\',\'desc\')">&#9660;</a></th>';
                $current++;
            }
        } else {
            // get headings from data
            foreach($data as $dataitem) {
                foreach($dataitem as $datakey => $dataproperty) {
                    echo '<th>'.$datakey.' <a href="#" onclick="sortdata(\''.$datakey.'\',\'asc\')">&#9650;</a><a href="#" onclick="sortdata(\''.$datakey.'\',\'desc\')">&#9660;</a></th>';
                }
                break;
            }
        }
        echo '</tr></thead>';
        
        ////// Add body //////
        echo '<tbody>';
            if($fn==null) {
                foreach($data as $dataitem) {
                    echo '<tr>';
                    foreach($dataitem as $datakey => $dataproperty) {
                        echo '<td>'.$dataproperty.'</td>';
                    }
                    echo '</tr>';
                }
            } else {
                foreach($data as $dataitem) {
                    if(function_exists($fn)) $fn($dataitem);
                    else echo 'Error: Function '.$fn.' not found!';
                }
            }
        echo '</tbody>';
        
        ////// Add footer (pagination) //////
        echo '<tfoot><tr><td colspan="999">';
        for($i=1; $i<=$totalpages; $i++) {
            echo '<a href="#" onclick="loadPage('.$i.','.$limit.'); return false;">'.$i.'</a> | ';
        }
        echo '</td></tr></tfoot>';
        
    }
    
}
?>