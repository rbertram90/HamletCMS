<?php
	// Get blog id
	if(!isset($_GET['blogid']) || $_GET['blogid'] <= 0) die('Unable to continue - no blog id found');
	$blogid = sanitize_number($_GET['blogid']);

	if(!isset($_GET['widget']) || strlen($_GET['widget']) == 0) die('Unable to continue - no widget found');
	$widgetname = sanitize_string($_GET['widget']);
	
	// if(!isset($_POST['location']) || strlen($_POST['location']) == 0) die('Unable to continue - no location found');
	// $location = sanitize_string($_POST['location']);
	
	// Get config
	// $config = sanitize_string($_POST['config']);
	
	// Split to array
	// $config = str_replace('&#34;', '"', $config);
	// $arrayConfig = json_decode($config, true);
	
	// Find widget type
	// $fieldtype = $arrayConfig['type'];
	
	// Get the definition
	$formhelper = new rbwebdesigns\HTMLFormsTools(null);
	
	$widgetConfigPath = SERVER_PATH_WIDGETS . '/' . $widgetname . '/config.json';
	
	// Get form definition
	if(!file_exists($widgetConfigPath)) die('Widget definition not found');
    
	$widgetConfig = rbwebdesigns\JSONhelper::jsonToArray($widgetConfigPath);
    
	// Add the action to the form
	// $arrayDef = json_decode($json, true);
	// $arrayDef['action'] = '/config/'.$blogid.'/widgets/'.$fieldtype.'/submit';
	// $arrayDef['formname'] = 'frm'.$fieldtype.'Settings';
	
	// Add a field for the location
	// $arrayDef['fields'][] = array(
	//	'type'  => 'hidden',
	//	'current' => $location,
	//	'name'  => 'sys_widget_location'
	//);
	
	// Add a field for the id
	//$arrayDef['fields'][] = array(
	//	'type'    => 'hidden',
	//	'name'    => 'sys_widget_id',
	//	'current' => '[!data.id]'
	//);
	
	// Add a field for the type
	//$arrayDef['fields'][] = array(
	//	'type'    => 'hidden',
	//	'name'    => 'sys_widget_type',
	//	'current' => '[!data.type]'
	//);
	
	// Output form
	echo $formhelper->generateFromJSON($widgetConfig['form-configuration'], $widgetConfig['defaults']);
?>