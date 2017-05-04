<?php
	// Get blog id
	if(!isset($_POST['blogid']) || $_POST['blogid'] <= 0) die('Unable to continue - no blog id found');
	$blogid = sanitize_number($_POST['blogid']);
	
	if(!isset($_POST['location']) || strlen($_POST['location']) == 0) die('Unable to continue - no location found');
	$location = sanitize_string($_POST['location']);
	
	// Get config
	$config = sanitize_string($_POST['config']);
	
	// Split to array
	$config = str_replace('&#34;', '"', $config);
	$arrayConfig = json_decode($config, true);
	
	// Find widget type
	$fieldtype = $arrayConfig['type'];
	
	// Get the definition
	$formhelper = new rbwebdesigns\HTMLFormsTools(null);
	
	$formpath = SERVER_ROOT.'/app/view/settings/widgets_'.$fieldtype.'.json';
	
	// Get JSON form definition
	if(!file_exists($formpath)) die('Widget definition not found');
	$json = file_get_contents($formpath);

	// Add the action to the form
	$arrayDef = json_decode($json, true);
	$arrayDef['action'] = '/config/'.$blogid.'/widgets/'.$fieldtype.'/submit';
	$arrayDef['formname'] = 'frm'.$fieldtype.'Settings';
	
	// Add a field for the location
	$arrayDef['fields'][] = array(
		'type'  => 'hidden',
		'current' => $location,
		'name'  => 'sys_widget_location'
	);
	
	// Add a field for the id
	$arrayDef['fields'][] = array(
		'type'    => 'hidden',
		'name'    => 'sys_widget_id',
		'current' => '[!data.id]'
	);
	
	// Add a field for the type
	$arrayDef['fields'][] = array(
		'type'    => 'hidden',
		'name'    => 'sys_widget_type',
		'current' => '[!data.type]'
	);
	
	// Output form
	echo $formhelper->generateFromJSON($arrayDef, $arrayConfig);
?>