<header id="global-header">

	<a href="http://www.rbwebdesigns.co.uk/">
		<img src="http://www.rbwebdesigns.co.uk/home/images/logo.png" alt="RBwebdesigns" border="0" />
	</a>
	
	<nav>
		<a href="http://www.rbwebdesigns.co.uk/">Home</a>
		<a href="http://www.rbwebdesigns.co.uk/projects.php">Web Portfolio</a>
		<a href="http://www.rbwebdesigns.co.uk/contact.php">Contact</a>
	</nav>
        
    {if isset($currentuser)}
    	<div id="current-user">
			{$currentuser.email}
			<a href="/account">Account</a>
			<a href="/logout">Logout</a>
		</div>
    {/if}
    
</header>

<div id="header-clear"></div>
