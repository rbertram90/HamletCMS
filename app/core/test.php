<!DOCTYPE html>
<html>
<head>
	<title>PHP test</title>
	<link rel="stylesheet" href="../resources/css/forms.css" />
	<script src="../resources/js/jquery-1.8.0.min.js"></script>
	<script src="../resources/js/validate.js"></script>
</head>
<body>
<?php
	
    include_once 'database.php';
    include_once 'dates.php';
    include_once 'image.php';
	include_once 'html_forms.php';
	include_once 'security.php';

	//$d = new DateFormatter();
	
	//echo $d->getAgeInYears("01-04-2001");
	
	
	$sec = new AppSecurity();
	
	$html = new HTMLFormsTools($sec);
	
	$data = array(
		'name' => 'Bill',
		'surname' => 'Gates',
		'username' => 'billgates01',
		'description' => 'Hi i\'m billy the programmer',
		'icon' => 'test01.png'
	);
	
	echo $html->generateFromJSON(file_get_contents("test.json"), $data);
		
	
	/*echo $html->createTextField("fld_test", "Question 1", "", array('helptext' => 'Further " information', 'placeholder'=> 'Hello World'));
	
	echo $html->createDropdown('fld_test', 'Question 2', array(
			'zero' => 'zero',
			'one' => 'one',
			'two' => 'two',
			'three' => 'three'
		), 'one');
	
	echo $html->createDropdownFromArray(array(
		'name' => 'fld_test',
		'caption' => 'Test',
		'values' => array(
			'0' => 'a',
			'1' => 'b',
			'2' => 'c',
			'3' => 'd',
			'4' => 'e'
		),
		'default' => 'None'
	));
		
	echo $html->createYearSelector();*/
		
	/*
    // Connect to database

    // Test Query - select signup_date for user id = 1
    $dateQuery = $db->selectSingleRow('users', 'signup_date', array('id' => 1));
    $signupdate = $dateQuery['signup_date'];

    $_df = new DateFormatter();
    echo $_df->formatFriendlyTime($signupdate);

    if(isset($_POST['imagesubmit'])) {
        $lobjImageProcessor = new ImageProcessor();
        $larrUpload = $lobjImageProcessor->upload('originals');
        if(strlen($larrUpload['fileerror']) > 0) echo $larrUpload['fileerror'];
        else {
            echo 'File upload successful<br/>'.$larrUpload['filepath'];
            $larrIcon = $lobjImageProcessor->createThumbnail($larrUpload['filepath'], 'thumbs/'.$larrUpload['filename']);
            echo 'Thumbnail created successfully<br/>';
        }
    }
	
	if(isset($_POST['multiimagesubmit'])) {
        $lobjImageProcessor = new ImageProcessor();
        $larrUpload = $lobjImageProcessor->multiUpload($_FILES);
	}*/
	
	
?>

<!--
<form action='test.php' method='POST' enctype='multipart/form-data'>
    <label for='file'>File (.jpg)</label>
    <input type='file' name='file' id='file' />
    <p><input type='submit' name='imagesubmit' value='submit' /></p>
</form>

<form action='test.php' method='POST' enctype='multipart/form-data'>
    <label for='upload'>Files</label>
	<input name="upload[]" type="file" multiple="multiple" />
    <p><input type='submit' name='multiimagesubmit' value='submit' /></p>
</form>
-->