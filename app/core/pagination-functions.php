<?php
/*
These functions could be used globally?
GET s = pagenum
*/

function getCurrentFullURL() {
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

function showPagination($pages,$pagenum) {
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
?>