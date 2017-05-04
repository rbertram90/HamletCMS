<?php

    $searchterm = sanitize_string($_POST['searchterm']);
    $users = $GLOBALS['modelUsers']->findUserByUsername($searchterm);

    if(count($users) > 0) {
        
        foreach($users as $user)
        {
            if($user['id'] != $_SESSION['userid'])
                $output = "<p><a href='#' onclick='$(\"#fld_contributorsearch\").val(this.innerHTML); $(\"#fld_contributor\").val(this.dataset.userid); return false;' data-userid='{$user['id']}'>{$user['username']}</a></p>";
        }

    } else {
        $output = "No Results";
    }

    echo $output;
?>