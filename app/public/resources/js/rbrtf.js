/**
	RB Rich Text Editor - jQuery plugin
	Designed to work with projects on rbwebdesigns.co.uk
	
	How to use:
	$("#textarea").rbrtf(PATH_TO_SERVER_ROOT);
**/

/*
var removeEmptyElements = function($elem) {
		$elem.children().each(function(i) {
			var tagName = $(this).prop('tagName');
			if(tagName == "P" || tagName == "STRONG") {
				if($(this).html() == "") {
					$(this).remove();
				}
			}
			removeEmptyElements($(this));
	});
}
*/


jQuery.fn.rbrtf = function(options) {

	var SERVER_ROOT = "", CONTENT = "", FIELD_WIKI = "rbrtf-wikiinput", CUSTOM_CONTROLS = "";
	var MODE = "r"; // r = rich text , w = wiki markup
	
	if(typeof options == "object") {
		if(options.urlroot) SERVER_ROOT = options.urlroot;
		if(options.content) CONTENT = options.content;
		if(options.rawvalueid) FIELD_WIKI = options.rawvalueid;
		if(options.defaultMode) MODE = options.defaultMode;
		if(options.customControls) CUSTOM_CONTROLS = options.customControls;
	}

	this.addClass("rbrtf-editor");
	
	// Create the buttons
    var lsHTML = "";
    // Controls
    lsHTML+= "<div class=\"rbrtf-document-controls\">";
	lsHTML+= "  <div style=\"float:right;\">";
    lsHTML+= "    <button class=\"update-source\">Wiki</button>";
    lsHTML+= "    <button class=\"update-richtext btn_disabled\" disabled='disabled'>RTF</button>";
    lsHTML+= "  </div>";
	lsHTML+= "  <div class=\"rbrtf-format-options\">";
    lsHTML+= "    <button data-tag=\"bold\" title=\"Bold\"><b>B</b></button>";
    lsHTML+= "    <button data-tag=\"italic\" title=\"Italic\"><i>I</i></button>";
    // lsHTML+= "    <button data-tag=\"underline\" title=\"Underline\"><u>U</u></button>";
    lsHTML+= "    <button data-tag=\"formatBlock\" data-options=\"<h1>\" title=\"Insert Heading 1\">H1</button>";
    lsHTML+= "    <button data-tag=\"insertUnorderedList\" title=\"Insert Bullet List\"><img src=\""+ SERVER_ROOT + "/resources/icons/list_32.png\" style=\"width:15px; height:15px;\" /></button>";
    lsHTML+= "    <button data-tag=\"insertOrderedList\" title=\"Insert Numbered List\"><img src=\""+ SERVER_ROOT + "/resources/icons/list_numbered_32.png\" style=\"width:15px; height:15px;\" /></button>";
	lsHTML+= 	  CUSTOM_CONTROLS;
	lsHTML+= "  </div>";
    lsHTML+= "</div>";
	
	// Add an element which is 'contenteditable' for the WYSIWYG editor
	lsHTML+= "<div contenteditable=true class=rbrtf-rtfinput>" + CONTENT + "</div>";
	
	// Setup the wiki textarea as required
	lsHTML+= "<textarea id='" + FIELD_WIKI + "' name='" + FIELD_WIKI + "' class='rbrtf-wikiinput'></textarea>";
	this.append(lsHTML);
	
	// Handle User Actions
	this.children('.rbrtf-document-controls').children('.rbrtf-format-options').children('button').on('click', function() {
		if($(this).attr("data-tag")) {
			document.execCommand($(this).attr("data-tag"), false, $(this).attr("data-options"));
		}
        return false;
	})
		
	
	// Switch to wiki markup view
    this.children('.rbrtf-document-controls').find('.update-source').click(function () {
	
		// removeEmptyElements($(".rbrtf-rtfinput"));
		
        var htmlcontent = $(".rbrtf-editor .rbrtf-rtfinput").html();
				
        jQuery.get(SERVER_ROOT + "/core/ajax/ajax_viewWikiMarkup.php", {content:htmlcontent}, function(data) {
            $("#" + FIELD_WIKI).val(data);
            rbrtf_showWiki();
        });
		
        return false;
    });
	
    // Switch to RTF view
    this.children('.rbrtf-document-controls').find('.update-richtext').click(function () {
        var wikicontent = $("#" + FIELD_WIKI).val();
        jQuery.get(SERVER_ROOT + "/core/ajax/ajax_viewTextOutput.php", {content:wikicontent}, function(data) {
            $(".rbrtf-editor .rbrtf-rtfinput").html(data);
            rbrtf_showRTF();
        });
        return false;
    });	
};
jQuery.fn.rbrtf_enableButton = function(options) {
    this.removeAttr("disabled");
    this.removeClass("btn_disabled");
};
jQuery.fn.rbrtf_disableButton = function(options) {
    this.attr("disabled", "disabled");
    this.addClass("btn_disabled");
};


var rbrtf_showRTF = function() {
    $(".rbrtf-editor .rbrtf-format-options").show();
    // $(".rbrtf-editor .wikihelp").hide();
    $(".rbrtf-editor .rbrtf-rtfinput").show();
    $(".rbrtf-editor .update-source").rbrtf_enableButton();
    $(".rbrtf-editor .update-richtext").rbrtf_disableButton();
    $(".rbrtf-wikiinput").hide();
};
var rbrtf_showWiki = function() {
    $(".rbrtf-editor .rbrtf-format-options").hide();
	
    $(".rbrtf-editor .rbrtf-rtfinput").hide();
    // $(".rbrtf-editor .wikihelp").show();
	
    $(".rbrtf-editor .update-source").rbrtf_disableButton();
    $(".rbrtf-editor .update-richtext").rbrtf_enableButton();
    $(".rbrtf-wikiinput").show();
};

var rbrtf_showWindow = function(location) {
	var newwindow = new rbwindow({'url': location});
	newwindow.show();
};