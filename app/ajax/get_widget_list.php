<?php
	// get location of widget
	if(!array_key_exists('location', $_GET)) die("No location found");
	$widgetlocation = sanitize_string($_GET['location']);
?>

<script type="text/javascript">
	function generateWidgetID() {
		return Date.now(); // simply return the time since 1970 - this will be unique enough for this purpose
	}

	function AddAndClose(widgetname) {
		
		var widgetid = generateWidgetID();
		var widgetlocation = "<?=$widgetlocation?>";
		
		html = "<div class='draggablewidget'>" + widgetname;
		
		html+= "<a onclick=\"showWidgetSettings('" + widgetname + "-" + widgetid + "'); return false;\" style=\"float:right;\">Options</a>";
		html+= "<textarea name=\"widgets[" + widgetlocation + "][" + widgetid + "]\" id=\"" + widgetname + "-" + widgetid + "-settingjson\" style=\"display:none;\">";
		html+= '{ "id": "' + widgetid + '", "type": "' + widgetname + '", "location": "' + widgetlocation + '" }';
		html+= "</textarea>";
		// delete option here...
		html+= "<input type=\"hidden\" id=\"" + widgetname + "-" + widgetid + "-widgetlocation\" value=\"header\" />";
		html+= "</div>";
		
		$("#<?=$elem?>").append(html);
		$(".rbwindow_screen").remove();
		$("html").css("overflow","visible");
		$("body").css("overflow","visible");
	}
</script>

<h3>Post List</h3>
<p>A list of posts categorised by date posted</p>
<button onclick="AddAndClose('postlist'); return false;">Add</button>

<h3>Tag List</h3>
<p>Display a list of post tags</p>
<button onclick="AddAndClose('taglist'); return false;">Add</button>

<h3>Owner Profile</h3>
<p>Display a short bio for the blog owner</p>
<button onclick="AddAndClose('profile'); return false;">Add</button>

<h3>Comments</h3>
<p>Show recent comments made on any post</p>
<button onclick="AddAndClose('comments'); return false;">Add</button>

<h3>Search</h3>
<p>Show a free-type search box</p>
<button onclick="AddAndClose('search'); return false;">Add</button>

<h3>Subscribers</h3>
<p>Show a list of users who have subscribed to your blog</p>
<button onclick="AddAndClose('subscribers'); return false;">Add</button>
