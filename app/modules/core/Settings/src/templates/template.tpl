<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", $blog->name, "/cms/settings/menu/{$blog->id}", 'Settings'), 'Template gallery')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Template gallery', 'sliders horizontal', $blog->name)}
        </div>
    </div>
    <div class="one column row">
        <div class="column">

            <div class="ui message">
                <p>When a new template is applied the system copies each of the files in the template so that you can then make changes without affecting the original template.</p>
                <a href="/cms/settings/templateConfig/{$blog->id}" class="ui button">Edit current template settings</a>
                <a href="/cms/settings/stylesheet/{$blog->id}" class="ui button">Edit current stylesheet</a>
            </div>
            <div class="ui warning message">
                <p><strong>Important</strong>: Applying a new Template will <strong>overwrite</strong> any changes you have made to the template settings or stylesheet editor. Widgets will also be cleared and will need re-adding through the widgets section.</p>
            </div>

            <h2>Core templates</h2>

            <div class="ui three cards">
            {foreach $core_templates as $template}
                <div class="ui card">
                    <div class="image">
                        <img src="{$template.thumbnail}" alt="{$template.name}">
                    </div>
                    <div class="content">
                        <div class="header">
                            <h3>{$template.name}</h3>
                        </div>
                        <div class="description">
                            <p>{$template.description}</p>
                        </div>
                    </div>
                    
                    <button class="ui bottom teal attached button js-apply-template" type="button" data-template-type="core" data-template-name="{$template.id}">
                        <i class="add icon"></i>
                        Apply to blog
                    </button>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <h2>Addon templates</h2>

            <div class="ui three cards">
            {foreach $addon_templates as $template}
                <div class="ui card">
                    <div class="image">
                        <img src="{$template.thumbnail}" alt="{$template.name}">
                    </div>
                    <div class="content">
                        <div class="header">
                            <h3>{$template.name}</h3>
                        </div>
                        <div class="description">
                            <p>{$template.description}</p>
                        </div>
                    </div>
                    
                    <button class="ui bottom teal attached button js-apply-template" type="button" data-template-type="addon" data-template-name="{$template.id}">
                        <i class="add icon"></i>
                        Apply to blog
                    </button>
                </div>
            {foreachelse}
                <div class="ui segment">
                    <p>There are no addon templates currently installed</p>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
</div>

<form method="POST" action="/cms/settings/template/{$blog->id}" id="updateTemplateForm">
    <input type="hidden" name="template_id" value="">
    <input type="hidden" name="template_type" value="">
</form>

<script>
$(".js-apply-template").click(function() {
    $("#updateTemplateForm input[name='template_id']").val($(this).data('template-name'));
    $("#updateTemplateForm input[name='template_type']").val($(this).data('template-type'));
    $("#updateTemplateForm").submit();
});
</script>

    {*
    <div class="template_wrapper">
        <h3>Black and Yellow</h3>
        <img src="/images/template_screenshots/black_and_yellow.png" alt="Black Template with yellow sub-colour" width="300" />
        <p>A night time feel blog template with hints of construction about it</p>
        <form method="post">
            <input type="hidden" value="tmplt_black_yellow" name="template_id" />
            <div class="push-right">
                <input type="submit" class="ui button teal" value="Apply to Blog" />
            </div>
        </form>
    </div>


    <div class="template_wrapper">
        <h3>Skate</h3>
        <img src="/images/template_screenshots/skate.png" alt="Screenshot of Skate Template" width="300" />
        <p>A black and white theme, inspired by skate culture</p>
        <form method="post">
            <input type="hidden" value="tmplt_skate" name="template_id" />
            <div class="push-right">
                <input type="submit" class="ui button teal" value="Apply to Blog" />
            </div>
        </form>
    </div>
    *}