{viewCrumbtrail(array("/overview/{$blog['id']}", $blog['name'], "/config/{$blog['id']}", 'Settings'), 'Widgets')}
{viewPageHeader('Widgets', 'oven_gear.png', $blog['name'])}

<!--
<p class="info">Drag the widgets between the enabled and disabled boxes to choose which ones should be showm and the order that they are in.</p>
-->

<style type="text/css">
    #disabledwidgetlist, #enabledwidgetlist
	{ldelim}
		width: 45%; display:inline-block; border:2px dashed #cccccc;
		border-radius:4px; padding:1%; margin:1%; height:300px;
		scroll:auto; vertical-align:top;
	{rdelim}
	
    #enabledwidgetlist:before, #disabledwidgetlist:before
	{ldelim}
		font-weight:bold; display:block; margin-bottom:10px;
	{rdelim}
	
    #enabledwidgetlist:before
	{ldelim}
		content:"Enabled Items";
	{rdelim}
	
    #disabledwidgetlist:before
	{ldelim}
		content:"Disabled Items";
	{rdelim}
	
    .draggablewidget
	{ldelim}
		background-color:#eee; border:1px solid #ddd; padding:4px;
		margin-bottom:8px; cursor:move;
	{rdelim}
</style>

<form action="{$clientroot_blogcms}/config/{$blog.id}/widgets/submit" method="POST">

    <div id="disabledwidgetlist">	
		{foreach from=$widgetconfig key=widgetname item=widgetsettings}

			{if array_key_exists('show', $widgetsettings) and $widgetsettings.show == 0}
				<div id="{$widgetname}" class="draggablewidget">{$widgetname}
					<input type="hidden" value="0" name="widgetfld_{$widgetname}_show" />
					<input type="hidden" value="0" name="widgetfld_{$widgetname}_order" />
					<a href="{$clientroot_blogcms}/config/{$blog.id}/widgets/{$widgetname}" style="float:right;">Settings</a>
				</div>
			{/if}

		{/foreach}

    </div><div id="enabledwidgetlist">

		{$count = 1}

		{foreach from=$widgetconfig key=widgetname item=widgetsettings}

			{if array_key_exists('order', $widgetsettings)}
				{$order = $widgetsettings.order}
			{else}
				{$order = $count}
			{/if}

			{if array_key_exists('show', $widgetsettings) and $widgetsettings.show == 1}
				<div id="{$widgetname}" class="draggablewidget">{$widgetname}
					<input type="hidden" value="1" name="widgetfld_{$widgetname}_show" />
					<input type="hidden" value="{$order}" name="widgetfld_{$widgetname}_order" />
					<a href="{$clientroot_blogcms}/config/{$blog.id}/widgets/{$widgetname}" style="float:right;">Settings</a>
				</div>
				{$count = $count + 1}
			{/if}

		{/foreach}

    </div>

    <div class="push-right">
        <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
        <input type="submit" value="Update" name="fld_submit" />
    </div>

</form>

<script src="{$clientroot}/resources/js/dragula.min.js"></script>
<link href="{$clientroot}/resources/css/dragula.min.css" rel="stylesheet" type="text/css"></script>

<script>
dragula([document.querySelector("#disabledwidgetlist"), document.querySelector("#enabledwidgetlist")], {ldelim}
   revertOnSpill: true
        
{rdelim}).on('drop', function(el, target, source, sibling) {ldelim}
    
    if(target.id == "disabledwidgetlist")
    {ldelim}
        el.childNodes[1].value = 0; // set to disabled
    {rdelim}
    
    else if(target.id == "enabledwidgetlist")
    {ldelim}
        el.childNodes[1].value = 1; // set to enabled
    {rdelim}
    
    // Update the orderby values
    enabledList = document.querySelector("#enabledwidgetlist");
    
    var j = 1;
    for(i = 0; i < enabledList.childNodes.length; i++)
    {ldelim}
        // console.log(enabledList.childNodes[i].className);
        className = "" + enabledList.childNodes[i].className
        if(className.indexOf("draggablewidget") >= 0)
        {ldelim}
            enabledList.childNodes[i].childNodes[3].value = j;
            j++;
        {rdelim}
    {rdelim}
    
{rdelim});    
</script>