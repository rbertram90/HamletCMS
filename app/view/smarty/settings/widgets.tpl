<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog['id']}", $blog['name'], "/config/{$blog['id']}", 'Settings'), 'Widgets')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Widgets', 'oven_gear.png', $blog['name'])}
        </div>
    </div>
</div>

<div class="ui secondary segment">Drag and drop widgets to re-order and change where on the page they are displayed. Widgets can be added to each section using the 'Add' buttons.</div>

<form action="/config/{$blog.id}/widgets/submit" method="POST">

    {foreach from=$widgetconfig key=section item=sectionwidgets}<div class="dropwrapper" id="{$section}dropwrapper">
            <div id="{$section}widgetlist" class="droparea" sectionname="{$section}">
                {foreach from=$sectionwidgets item=widget}
                    <div class="draggablewidget" id="{$widget.type}-{$widget.id}">{$widget.type}
                        <div style="float:right;">
                            <a onclick="showWidgetSettings('{$widget.type}-{$widget.id}'); return false;"><img src="/resources/icons/16/pencil.png" alt="Options" title="Widget Settings"></a>
                            <a onclick="removeWidget('{$widget.type}-{$widget.id}'); return false;"><img src="/resources/icons/16/cross.png" alt="Remove" title="Remove"></a>
                        </div>
                        <textarea id="{$widget.type}-{$widget.id}-settingjson" name="widgets[{$section}][{$widget.id}]" style="display:none;">{json_encode($widget)}</textarea>
                        <input type="hidden" id="{$widget.type}-{$widget.id}-widgetlocation" value="{$section}" />
                    </div>
                {/foreach}
            </div>
            <button class="ui button" onclick="showWidgets('{$section}widgetlist', '{$section}'); return false;">Add Widget</button>
    </div>{/foreach}

    
    <input type="button" class="ui button right floated" value="Cancel" name="goback" onclick="window.history.back()" />
    <input type="submit" class="ui button teal right floated" value="Update" name="fld_submit" />
    

</form>

<script src="/resources/js/dragula.min.js"></script>
<link href="/resources/css/dragula.min.css" rel="stylesheet" type="text/css" />

<script>
dragula([document.querySelector("#headerwidgetlist"),document.querySelector("#footerwidgetlist"),document.querySelector("#leftpanelwidgetlist"),document.querySelector("#rightpanelwidgetlist")], {ldelim}
    revertOnSpill: true
    
{rdelim}).on('drop', function(el, target, source, sibling) {ldelim}
    var sectionname = $(target).attr('sectionname');
    // Set the location in the form
    $('#' + el.id + '-widgetlocation').val(sectionname);
    $('#' + el.id + '-settingjson').attr('name', 'widgets[' + sectionname + '][' + el.id + ']');
    jsonstring = $('#' + el.id + '-settingjson').val();
    jsonarray = $.parseJSON(jsonstring);
    jsonarray['location'] = sectionname; // update location name in json
    $('#' + el.id + '-settingjson').val(JSON.stringify(jsonarray));
{rdelim});    
</script>

<script>
    /**
        Show the 'add new widget' popup
    **/
    function showWidgets(elem, location) {
        var newWindow = new rbwindow({
            url: '/ajax/widget_list?element=' + elem + '&location=' + location
        });
        newWindow.show();
    }
    
    function removeWidget(widgetid) {
        // Remove the element from dom
        $('#' + widgetid).remove();
    }
    
    /**
        Load the widget settings screen
    **/
    function showWidgetSettings(elemid) {
        var config = "config=" + $('#' + elemid + '-settingjson').val(); // do we need to get this from client side?
        config += "&blogid=" + {$blog['id']};
        config += "&location=" + $('#' + elemid + '-widgetlocation').val();
        
        // Load content and show in popup window
        ajax_PostRequest('/ajax/widget_settings', config, function(data) {            
            var newWindow = new rbwindow({
                'htmlContent': data
            });
            newWindow.show();
        });
    }
    
    /**
        Save the widget settings
    **/
    var saveWidgetSettings = function(type, form) {
        ajax_PostRequest("/config/{$blog.id}/widgets/" + type + "/submit", encapsulateForm(form), function(data) {
            // Save Complete
            $(".rbwindow_screen").remove();
            $("html").css("overflow","visible");
            $("body").css("overflow","visible");
            
            // Update the client side config json
            var newjson = $.parseJSON(data);
            $('#' + newjson.type + '-' + newjson.id + '-settingjson').val(data);
        });
    };
</script>