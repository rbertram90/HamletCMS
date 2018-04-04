<?php
/**
 * Helper function to keep the title consistant accross the website
 * 
 * @param string $title
 *   required - text to show as title
 * @param string $icon
 *   required - name of icon in resources/icons/64 folder
 * @param string $subtitle
 *   optional - string to show as subtitle
 * 
 * @return - string - HTML to echo
 */
function viewPageHeader($title, $icon, $subtitle='') {

    if($subtitle==''):
        return <<<EOD
     <img src="/resources/icons/64/{$icon}" class="settings-icon" /><h1 class="settings-title">{$title}</h1>
EOD;

    else:
        return <<<EOD
     <img src="/resources/icons/64/{$icon}" class="settings-icon" /><h1 class="settings-title" style="margin-top:0px; line-height:32px; margin-bottom:16px;">{$title}<br/><span class="subtitle">{$subtitle}</span></h1>
EOD;
    endif;
}

/**
 * Helper function to keep crumbtrail consistant
 * 
 * @param array $path
 *   array with alternating url and name values for each link in the crumbtrail
 * @param string $currentPage
 *   label for the current page
 * 
 * @return string
 *   HTML for crumbtrail
 */
function viewCrumbtrail($path, $currentpage) {

    $output = <<<EOD
        <div class="ui breadcrumb"><a href="/" class="section">Home</a>
EOD;

    for($i = 0; $i < count($path) - 1; $i=$i+2):
    
        $output.= <<<EOD
        <i class="right angle icon divider"></i>
<a href="{$path[$i]}" class="section">{$path[$i+1]}</a>
EOD;
        
    endfor;
    
    return $output.'<i class="right angle icon divider"></i><div class="active section">'.$currentpage.'</div></div>';
}
