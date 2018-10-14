{*
    @file widgets3.tpl

    This file is the front-end code to manage widgets within the blog,
    it is a complicated beast!

    Adding, editing and deleting widgets are all done client side and
    then posted to the server in one request. The widgets are
    re-arranged and then whatever data is passed to the server it will
    save - if anything is missing it doesn't care.

    Each widget has a single textarea component in which its config
    is held. The name of the field defines which section and the type
    of widget it is.

    Widget definition is in json files in WIDGET_ROOT
    Data is saved to json file in blogdata/<blogid>/widgets.json

    Known things to fix/think about:
    - Need to stop user adding two of the same widget in the same section
    -  (or) allow the user to add multiple widgets - needs ID
    - Widget captions is just the type - needs cleaning up
    - Widgets not currently implemented!!! - done by the .tpl in folder
*}
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog['id']}", $blog['name'], "/cms/settings/menu/{$blog['id']}", 'Settings'), 'Widgets')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {* Header *}
            {viewPageHeader('Widgets', 'sliders horizontal', $blog['name'])}
    
            <form method="post" id="configureWidgetsMainForm">
            
                {* Save Message *}
                <div class="ui yellow clearing message" id="unsaved-changes" style="display:none;">
                    <button class="ui button green right floated" type="submit">Save Changes</button>
                    <div class="header">
                    Save your changes
                    </div>
                    Widgets will not update on the blog until changes are saved
                </div>

                <div class="ui segments" style="background-color:#fff;">
                    {foreach from=$widgetconfig key=sectionname item=section}
                    {* Add Widget Button *}
                    <div class="ui clearing segment">
                        <h3 class="ui left floated header">{$sectionname}</h3>
                        <button class="ui right floated basic green icon button" type="button" onclick="$('#widget_location').val('{$sectionname}'); $('#addWidgetPopup').modal('show');">
                            <i class="plus icon"></i> Add Widget
                        </button>
                    </div>
                    {if count($section) == 0}
                    {* Empty Section *}
                    <div id="{$sectionname}-widgetlist" data-sectionname="{$sectionname}" class="ui segments" data-empty="true">
                        <div class="ui segment" data-placeholder="true"><i style="color:grey;">Empty</i></div>
                    </div>
                    {else}
                    {* Section *}
                    <div id="{$sectionname}-widgetlist" data-sectionname="{$sectionname}" class="ui segments" data-empty="false">
                        {foreach from=$section key=widgettype item=widget}
                        <div data-saved="true" class="ui segment clearing widget_placeholder" data-widgetsection="{$sectionname}" data-widgetname="{$widgettype}">
                            <button type="button" class="ui right floated icon negative basic button" onclick="submitRemoveWidget(this);"><i class="remove icon"></i> Remove</button>
                            <button type="button" class="ui right floated icon blue basic button" onclick="runConfigureWidgetForm(this);"><i class="edit icon"></i> Edit</button>
                            <p><i class="move grey icon"></i> {$widgettype}</p>
                            {*
                                This textarea is the full data source for each widget
                                The value and the name are both important - the name will dynamically change
                                when widget is dropped into another section
                            *}
                            <textarea style="display:none;" class="widgetconfigjson" name="widgets[{$sectionname}][{$widgettype}]">{json_encode($widget, JSON_PRETTY_PRINT)}</textarea>
                        </div>
                        {/foreach}
                    </div>
                    {/if}
                    {/foreach}
                </div>
            
            </form>
                
        </div>
    </div>
</div>

{* Add Widget Form *}
<div class="ui modal" id="addWidgetPopup">
    <i class="close icon"></i>
    <div class="header">
        Add Widget
    </div>
    <div class="content">
        {foreach from=$installedwidgets key=name item=widget}
            <div class="ui clearing segment">
                <button class="ui right floated icon green button" onclick="submitAddWidget(this);" data-widgetname="{$name}" data-widgettitle="{$widget.name}"><i class="plus icon"></i> Add</button>
                <div class="widget_config" style="display:none;">{$widget._settings_json}</div>
                <h3>{$widget.name}</h3>
                <p>{$widget.description}</p>
            </div>
        {/foreach}
    </div>
    <div class="actions">
        <div class="ui deny button">
            Close
        </div>
    </div>
</div>

{* Configure Widget Form *}
<div class="ui modal" id="editWidgetPopup">
    <i class="close icon"></i>
    <div class="header">
        Configure Widget
    </div>
    <div class="content">
        
    </div>
    <div class="actions">
        <div class="ui positive button" onclick="$('#editWidgetPopup .content form').submit();">
            Apply
        </div>
        <div class="ui deny button">
            Close
        </div>
    </div>
</div>

<input type="hidden" id="widget_location" name="widget_location" value="">

<script src="/resources/js/dragula.min.js"></script>
<link href="/resources/css/dragula.min.css" rel="stylesheet" type="text/css" />

<script>
function submitAddWidget(buttonElement) {
    
    var label = $(buttonElement).data('widgettitle');
    var widgetName = $(buttonElement).data('widgetname');
    var locationName = $('#widget_location').val();
    var targetSection = $('#' + locationName + '-widgetlist');
    var widgetConfigJSON = $(buttonElement).siblings('.widget_config').html();
    
    // HTML to add 
    var widgetPlaceholder = '<div data-saved="false" class="ui segment clearing widget_placeholder" data-widgetsection="' + locationName + '" data-widgetname="' + widgetName + '">' + 
        '<button type="button" class="ui right floated icon negative basic button" onclick="submitRemoveWidget(this);"><i class="remove icon"></i> Remove</button>' +
        '<button type="button" class="ui right floated icon blue basic button" onclick="runConfigureWidgetForm(this);"><i class="edit icon"></i> Edit</button>' +
        '<p><i class="move grey icon"></i> ' + label + '</p>' +
        '<textarea style="display:none;" class="widgetconfigjson" name="widgets[' + locationName + '][' + widgetName + ']">' +
        widgetConfigJSON + '</textarea>' +
    '</div>';
    
    // Either append or overwrite section content
    if(targetSection.data('empty')) targetSection.html(widgetPlaceholder);
    else targetSection.append(widgetPlaceholder);
    
    // Mark the section as having valid widgets
    targetSection.data('empty', false);
    
    // Hide the popup
    $('#addWidgetPopup').modal('hide');
    
    // Show the un-saved changes section
    $('#unsaved-changes').show();
}

    
function runConfigureWidgetForm(buttonElement) {
    
    var buttonParent = $(buttonElement).parent();
    var widgetName = $(buttonParent).data('widgetname');
    var widgetSection = $(buttonParent).data('widgetsection');
    
    $.get('/cms/settings/configurewidget/{$blog['id']}', { widget: widgetName }, function(data) {
        $('#editWidgetPopup .content').html(data);
        
        var editForm = $('#editWidgetPopup .content form');
        editForm.data('widgetname', widgetName);
        editForm.data('widgetsection', widgetSection);
        
        var currentValues = JSON.parse($(buttonElement).siblings(".widgetconfigjson").val());
        
        for(var item in currentValues) {
            $('#editWidgetPopup .content form #widget\\[' + item + '\\]').val(currentValues[item]);
        }
    });
    
    $('#editWidgetPopup').modal('show');
}
    
function submitConfigureWidgetForm(form) {
    
    var widgetSection = $(form).data('widgetsection');
    var widgetName = $(form).data('widgetname');
    
    var widgetJson = '{ldelim}';
    
    for(var i = 0, element; element = form.elements[i++];) {
        if(element.name.substring(0, 6) == 'widget') {
            fieldName = element.name.slice(7, -1);
            fieldValue = element.value;
            
            // Add quotes for string
            if(!$.isNumeric(fieldValue)) {
                fieldValue = '"' + fieldValue + '"';
            }
            
            widgetJson += '"' + fieldName + '": ' + fieldValue + ',';
        }
    }
    
    // trim last comma
    widgetJson = widgetJson.slice(0, -1);
    widgetJson += '{rdelim}';
    
    // Copy json back to main form
    $('#configureWidgetsMainForm textarea[name="widgets[' + widgetSection + '][' + widgetName + ']"]').val(widgetJson);
    
    // Show the submit form button
    $('#unsaved-changes').show();
    
    // Don't submit form
    return false;
}
    
    
function submitRemoveWidget(buttonElement) {
    
    var fieldParent = $(buttonElement).parent();
    var section = fieldParent.parent();
    
    if(!confirm('Are you sure you wish to delete this widget?')) return false;
    
    // Check if it had previously been saved to database
    if(fieldParent.data('saved')) {
        // Yes -Show the un-saved changes section
        $('#unsaved-changes').show();
    }
    
    // Remove from DOM
    fieldParent.remove();
    
    // Add in placeholder if needed
    if(section.children().length == 0) {
        section.html('<div class="ui segment" data-placeholder="true"><i style="color:grey;">Empty</i></div>');
    }
}
    

// Define the droppable areas
var drake = dragula([document.querySelector("#Header-widgetlist"),document.querySelector("#Footer-widgetlist"),document.querySelector("#LeftPanel-widgetlist"),document.querySelector("#RightPanel-widgetlist")], {
    revertOnSpill: true
    
}).on('drop', function(el, target, source, sibling)
{
    // Element Dropped

    // Check if the element item was drag from is now empty
    if($(source).children('.ui.segment').length == 0)
    {
        // Add a placeholder empty item
        $(source).html('<div class="ui segment" data-placeholder="true"><i style="color:grey;">Empty</i></div>');
        
        // Mark the section as having no valid widgets
        $(source).data('empty', true);
    }

    // Check for empty placeholders in new element & remove
    $segments = $(target).children('.ui.segment')
    elemCount = $segments.length;
    for(var n = 0; n < elemCount; n++)
    {
        elem = $segments.get(n);
        if(elem.dataset.placeholder) elem.remove();
    }

    // Remove the data-empty attribute on target
    $(target).data('empty', false);

    // Change the data-widgetsection attribute on element
    var newSectionName = $(target).data('sectionname');
    $(el).data('widgetsection', newSectionName);
    $(el).attr('data-widgetsection', newSectionName); // useful but not necessary as data already processed
    var widgetName = $(el).data('widgetname');
    $(el).find('.widgetconfigjson').attr('name', 'widgets[' + newSectionName + '][' + widgetName  + ']');
    
    // Show the un-saved changes section
    $('#unsaved-changes').show();
});
    
drake.on('over', function(el, source)
{
    if(el.dataset.placeholder)
    {
        drake.cancel(true); // can't move the 'empty' segments
    }
});
</script>