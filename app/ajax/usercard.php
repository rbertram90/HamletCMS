 <?php
    $userid = sanitize_number($params[1]);

    $user = $GLOBALS['modelUsers']->getUserById($userid);

    if(strtolower(getType($user)) == 'array') {

        if(strpos($user['profile_picture'], 'default') !== FALSE)
        {
            $output = '<img src="/avatars/profile_default.jpg" alt="[Profile Photo]" class="user-card-icon" />';
        }
        else
        {
            $output = '<img src="/avatars/thumbs/'.$user['profile_picture'].'" alt="[Profile Photo]" class="user-card-icon"/>';
        }

        $output.= '<h4>'.$user['username'].'</h4>';
        $output.= '<p>'.$user['name'].' '.$user['surname'].'</p>';

    } else {
        $output = "Error - User '{$params[1]}' not found! (".getType($user).")";
    }

echo $output;
?>