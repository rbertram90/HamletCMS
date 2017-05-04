<?php
    // Include Setup
    include_once 'ajax_setup.inc.php';

    // Sanitize Blog ID
    $blogid = safeNumber($_GET['blogid']);

    // Try to add to favorites outputing any messages that are returned
    echo $mdl_blogs->removeFavourite($_SESSION['userid'], $_GET['blogid']);
?>