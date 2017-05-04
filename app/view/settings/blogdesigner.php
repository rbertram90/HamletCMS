<?php
/*********************************************************
Front End for Blog Designer UI
@author     R.Bertram
@date       08/02/2014

****************************************************************************************
    1. Colour Picker
***************************************************************************************/

$GLOBALS['previewUpdateJS'] = "";

function field_colourPicker($options, $group, $classname) {
        
	// Create value for name attribute
	// $group = str_replace(" ", "_", $group);
	$fieldName = str_replace(" ", "_", $options['label']);
	$fieldID = str_replace(" ", "_", $group."-".$options['label']);
    $fieldchecked = ($options['default'] === $options['current']) ? " checked" : "";
    $fielddisabled = ($options['default'] === $options['current']) ? " field_disabled" : "";
    $group = strtolower($group);
        
    $rules = array(
        'bgcolor' => 'background-color',
        'color' => 'color'
    );
    
    $GLOBALS['previewUpdateJS'].= "\t$('.{$classname}', previewDocument).css('{$rules[$options['type']]}', '#' + $('#displayfield-{$fieldID}').val());" . PHP_EOL;
    
echo <<<EOD

	<label for="labelfield-{$fieldID}">{$options['label']}</label>
	<div class="default_check">
	   <input type="checkbox" id="defaultfield-{$fieldID}" name="defaultfield[{$group}][{$fieldName}]" class="fld_check_default"{$fieldchecked} onchange="window.updatePreview();" />Default
    </div>
	<input class="color{$fielddisabled}" type="text" data-fieldtype="{$options['type']}" data-default="{$options['default']}" id="displayfield-{$fieldID}" name="displayfield[{$group}][{$fieldName}]" value="{$options['current']}" onchange="window.updatePreview();">
    
EOD;
}

/***************************************************************************************
    2. Font Field
***************************************************************************************/

function field_fontPicker($options, $group, $classname) { 

	// Create value for name attribute
	// $group = str_replace(" ", "_", $group);
	$fieldName = str_replace(" ", "_", $options['label']);
	$fieldID = str_replace(" ", "_", $group."-".$options['label']);
    $fieldDefault = ($options['default'] == $options['current']) ? " checked" : "";
	$fieldDisabled = ($options['default'] === $options['current']) ? " class='field_disabled'" : "";
    
    // $GLOBALS['previewUpdateJS'] = $GLOBALS['previewUpdateJS'] . "\tpreviewDocument.querySelector('.{$classname}').style.fontFamily = document.getElementById('displayfield-{$fieldID}').options[document.getElementById('displayfield-{$fieldID}').selectedIndex].dataset.family;" . PHP_EOL;
    
    $GLOBALS['previewUpdateJS'].= "\t$('.{$classname}', previewDocument).css('font-family', $('#displayfield-{$fieldID}')[0].options[$('#displayfield-{$fieldID}')[0].selectedIndex].dataset.family);" . PHP_EOL;
    
echo <<<EOD

	<label for="displayfield-{$fieldID}">{$options['label']}</label>
	<div class="default_check">
		<input type="checkbox" name="defaultfield[{$group}][{$fieldName}]" id="defaultfield-{$fieldID}" class="fld_check_default"{$fieldDefault} onchange="window.updatePreview();" />Default
	</div>
	<select name="displayfield[{$group}][{$fieldName}]" data-fieldtype="{$options['type']}" data-default="{$options['default']}" id="displayfield-{$fieldID}"{$fieldDisabled} onchange="window.updatePreview();">
		<option value="ARIAL" data-family="Arial, Helvetica, sans-serif" style="font-family:Arial, Helvetica, sans-serif;">Arial, Helvetica, sans-serif</option>
		<option value="CALIBRI" data-family="Calibri, sans-serif" style="font-family:Calibri, sans-serif;">Calibri, sans-serif</option>
		<option value="COMICSANS" data-family="'Comic Sans MS', cursive" style="font-family:'Comic Sans MS', cursive;">Comic Sans MS, cursive</option>
		<option value="COURIER" data-family="'Courier New', monospace" style="font-family:'Courier New', monospace;">Courier New, monospace</option>
		<option value="IMPACT" data-family="Impact, Charcoal, sans-serif" style="font-family: Impact, Charcoal, sans-serif;">Impact, Charcoal, sans-serif</option>
		<option value="LUCIDA" data-family="'Lucida Console', Monaco, monospace" style="font-family:'Lucida Console', Monaco, monospace;">Lucida Console, Monaco, monospace</option>
		<option value="TAHOMA" data-family="Tahoma, Geneva, sans-serif" style="font-family:Tahoma, Geneva, sans-serif;">Tahoma, Geneva, sans-serif</option>
		<option value="TREBUCHET" data-family="'Trebuchet MS', sans-serif" style="font-family:'Trebuchet MS', sans-serif;">Trebuchet MS, sans-serif</option>
	</select>
	<script type="text/javascript">
	$(function() { $("#displayfield-{$fieldID}").val('{$options['current']}'); });
	</script>

EOD;
}

/***************************************************************************************
    3. Font Size Picker
***************************************************************************************/

function field_fontSize($options, $group, $classname) {
    
	// Create value for name attribute
	// $group = str_replace(" ", "_", $group);
	$fieldName = str_replace(" ", "_", $options['label']);
	$fieldID = str_replace(" ", "_", $group."-".$options['label']);
    $fieldDefault = ($options['default'] == $options['current']) ? " checked" : "";
    $fieldDisabled = ($options['default'] === $options['current']) ? "field_disabled" : "";
    
    $GLOBALS['previewUpdateJS'].= "\t$('.{$classname}', previewDocument).css('font-size', $('#displayfield-{$fieldID}').val() + 'px');" . PHP_EOL;
    
echo <<<EOD

	<label for="labelfield-{$fieldName}">{$options['label']}</label>
	<div class="default_check">
	   <input type="checkbox" name="defaultfield[{$group}][{$fieldName}]" id="defaultfield-{$fieldID}" class="fld_check_default"{$fieldDefault} onchange="window.updatePreview();" />Default
    </div>
	<select class="{$fieldDisabled}" data-fieldtype="{$options['type']}" data-default="{$options['default']}" id="displayfield-{$fieldID}" name="displayfield[{$group}][{$fieldName}]" onchange="window.updatePreview();">
		<option value="10">10px</option>
		<option value="12">12px</option>
		<option value="14">14px</option>
		<option value="16">16px</option>
		<option value="20">20px</option>
        <option value="24">24px</option>
        <option value="28">28px</option>
        <option value="32">32px</option>
        <option value="36">36px</option>
        <option value="40">40px</option>
	</select>
	<script type="text/javascript">
	$(function() { $("#displayfield-{$fieldID}").val('{$options['current']}'); });
	</script>

EOD;
}

?>

<div class="crumbtrail">
	<a href="/">Home</a><a href="/overview/<?=$blog['id'] ?>"><?=$blog['name']?></a><a href="/config/<?=$blog['id'] ?>">Settings</a><a>Blog Design</a>
</div>

<img src="/resources/icons/64/paintbrush.png" class="settings-icon" /><h1 class="settings-title">Customise Blog Design</h1>

<style>
    .default_check { font-size:14px; }
    .field_disabled { background-color:#eee !important; color:#ccc !important; }
    #previewwindow { border:1px solid #ccc; width:67%; display:inline-block; vertical-align: top; box-sizing:border-box; height:70vh; }
    #designoptions { width:32%; display:inline-block; vertical-align: top; box-sizing:border-box; padding-right:20px; }
</style>

<script type="text/javascript">
$(document).ready(function() {
        
	function disableField($elem) {
		var valuedefault = $elem.attr('data-default').toUpperCase();
		$elem.val(valuedefault);
		$elem.addClass('field_disabled').attr('style','');
	}
	function enableField($elem) {
		$elem.removeClass('field_disabled').css('background-color','#' + $elem.val());
			//.prop('disabled', false)
	}

	$(".fld_check_default").click(function() {
	
		var firstDash = $(this).attr("id").indexOf("-");
		var commonName = $(this).attr("id").substring(firstDash);
		var $elem = $("#displayfield" + commonName);

		if($(this).is(':checked')) {
			disableField($elem);
		} else {
			enableField($elem);
		}
	});
});
</script>

<div style="clear:right;"></div>

<div id="designoptions">
    <form action="/config/<?=$blog['id']?>/blogdesigner/submit" method="post">
<?php
    // Open JSON file here
    (array) $design_settings = jsonToArray(SERVER_PATH_BLOGS . '/' . $blog['id'] . '/template_config.json');

    // loop through sections (head,posts,footer...)
    foreach($design_settings as $key => $section):

        // Special Case
        if(strtolower($key) == 'layout') continue;

        echo "<h3>".$key."</h3>";

        echo "<input type='hidden' value='$section[0]' name='classname-$key'>";

        // loop through each rule in this section
        foreach($section as $rulekey => $rule):

            // Ignore if not array
            if(gettype($rule) !== "array") continue;

            switch($rule['type']):
                case "color":
                case "bgcolor":
                    field_colourPicker($rule, $key, $section[0]);
                    break;

                case "font":
                    field_fontPicker($rule, $key, $section[0]);
                    break;

                case "textsize":
                    field_fontSize($rule, $key, $section[0]);
                    break;

                default:
                    // do nothing!
                    break;
            endswitch;
        endforeach;
    endforeach;
?>

        <div class="push-right">
            <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
            <input type="submit" value="Save Changes" />
        </div>
    </form>
</div>

<script>
var updatePreview = function() {
    var previewDocument = document.getElementById('previewwindow').contentDocument;
<?=$GLOBALS['previewUpdateJS']?>
};
window.updatePreview = updatePreview;
</script>

<iframe id="previewwindow" name="previewwindow" src="/blogs/<?=$blog['id']?>"></iframe>