<?php
    // Include CMS
    if(defined("EXTERNAL_DOMAIN") && EXTERNAL_DOMAIN === 1):
        $blogroot = '/';
    else:
        $blogroot = "/blogs/".$DATA['blog_key'];
    endif;

    $log = "";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=$DATA['page_title']?> - powered by the Blog CMS by RBwebdesigns</title>
	<meta charset="UTF-8">
    
    <!--CSS Includes-->
    <?php $log .= importStylesheets($DATA['includes_css'])?>
    
    <!--JS Includes-->
    <?php $log .= importJavascript($DATA['includes_js'])?>
    
    <script>
    Galleria.loadTheme('/resources/js/galleria.classic.min.js');
    // Galleria.run('.galleria');
    </script>

<?php
      if(defined("EXTERNAL_DOMAIN") && EXTERNAL_DOMAIN === 1):

          // <link rel="stylesheet" href="/default.css" type="text/css" />
          $css = file_get_contents(SERVER_PATH_BLOGS.'/'.$DATA['blog_key'].'/default.css');
          echo '<style>'.$css.'</style>';

      else:
          // <link rel="stylesheet" href="CLIENT_ROOT_BLOGCMS/data/blogs/$DATA['blog_key']/default.css" type="text/css" />
		  
          $css = file_get_contents(SERVER_PATH_BLOGS.'/'.$DATA['blog_key'].'/default.css');
          echo '<style>'.$css.'</style>';

      endif;
?>
    <!-- Custom CSS -->
    <style type="text/css" id="customcss"><?=$DATA['custom_css']?></style>
    
    <?=$DATA['page_headerbackground']?>
	
	<script type="text/javascript">
		var folder_root = "/projects/blog_cms";
	</script>
</head>
<body>
<!--RBwebdesigns Branding-->
<?php
    include SERVER_ROOT.'/app/core/view/page-header.php';
?>

    <div class="wrapper">

        <div class="header">            
            <?php if(!$DATA['header_hide_title']): ?>
            <h1><a href="<?=$blogroot?>"><?=$gobjBlog['name']?></a></h1>
            <?php endif; ?>
            
            <?php if(!$DATA['header_hide_description']): ?>
            <h2><?=$gobjBlog['description']?></h2>
            <?php endif; ?>
			
			<?php if(array_key_exists('header', $DATA['widget_content'])) echo $DATA['widget_content']['header']; ?>
        </div>
		
		<div class="navigation">
			<?=$DATA['page_navigation']?>
		</div>

		<?php
			$columncount = 2;
			$postcolumn = 1;
		
			if(strtolower(getType($DATA['template_config'])) == 'array')
			{
				if(array_key_exists('Layout', $DATA['template_config']))
				{
					if(array_key_exists('ColumnCount', $DATA['template_config']['Layout']))
					{
						$columncount = $DATA['template_config']['Layout']['ColumnCount'];
					}
					
					if(array_key_exists('PostsColumn', $DATA['template_config']['Layout']))
					{
						$postcolumn = $DATA['template_config']['Layout']['PostsColumn'];
					}
				}
			}
            
			switch($columncount)
			{
				case 1:
					include SERVER_ROOT.'/app/view/blog_body_singlecolumn.php';
					break;
				case 3:
					if($postcolumn == 1) include SERVER_ROOT.'/app/view/blog_body_threecolumns_left.php';
					elseif($postcolumn == 2) include SERVER_ROOT.'/app/view/blog_body_threecolumns_centre.php';
					else include SERVER_ROOT.'/app/view/blog_body_threecolumns_right.php';
					break;
				case 2:
				default:
					if($postcolumn == 1) include SERVER_ROOT.'/app/view/blog_body_twocolumns_left.php';
					else include SERVER_ROOT.'/app/view/blog_body_twocolumns_right.php';
					break;
			}
		?>

		<div class="footer">
			<?php if(array_key_exists('footer', $DATA['widget_content'])) echo $DATA['widget_content']['footer']; ?>
            <div class="custom_footer_content"><?=$DATA['page_footercontent']?></div>
			2012 - 2017 RBwebdesigns &copy;
		</div>
    </div>
</body>
</html>