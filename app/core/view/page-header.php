<?php
    // Just incase - this uses the live website definition
    if(!defined("CLIENT_ROOT")) define("CLIENT_ROOT", '');
      
      
    if(defined("IS_DEVELOPMENT") && IS_DEVELOPMENT)
    {
        $rootservername = "rbwebdesigns.local";
    }
    else
    {
        $rootservername = "www.rbwebdesigns.co.uk";
    }
    
    if($_SERVER['SERVER_NAME'] != $rootservername)
    {
        if(defined("CLIENT_ROOT_ABS"))
        {
            define('LINKPATH', CLIENT_ROOT_ABS);
        }
        else
        {
            define('LINKPATH','http://www.rbwebdesigns.co.uk');
        }
    }
?>

<header id="global-header">

	<a href="<?=LINKPATH?>/index.php">
		<img src="<?=LINKPATH?>/home/images/logo.png" alt="RBwebdesigns" border="0" />
	</a>
	
	<nav>
		<a href="http://www.rbwebdesigns.co.uk/">Home</a>
		<a href="http://www.rbwebdesigns.co.uk/projects.php">Portfolio</a>
		<a href="http://www.rbwebdesigns.co.uk/contact.php">Contact</a>
	</nav>
    
	<?php if(isset($_SESSION['userid'])):
		if(!isset($mdlUsers)) {
            
			if(!isset($db)) {
                $jsonhelper = new \rbwebdesigns\JSONhelper();
                $config = $jsonhelper->jsonToArray(SERVER_ROOT . '/app/config/config.json');
                $databaseCredentials = $config['database'];
				$db = new rbwebdesigns\DB($databaseCredentials['server'], $databaseCredentials['user'], $databaseCredentials['password'], $databaseCredentials['name']);
			}
            
            $mdlUsers = new rbwebdesigns\Users($db);
		}
		$user = $mdlUsers->getUserById($_SESSION['userid']);
	?>
		
		<div id="current-user">
			<?php echo $user['email']; ?>
			<a href="/account">Account</a>
			<a href="/logout">Logout</a>
		</div>

	<?php endif; ?>
</header>

<div id="header-clear"></div>
