<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog['id']}", $blog['name'], "/cms/settings/menu/{$blog['id']}", 'Settings'), 'Template Gallery')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Template Gallery', 'star_doc.png', $blog['name'])}
        </div>
    </div>
</div>


<div class="ui warning message">
    <p><strong>Important</strong>: Applying a new Template will <strong>overwrite</strong> any changes you have made using the blog designer or stylesheet editor.</p>
    <p>Widgets will also be cleared and will need re-adding through the widgets section.</p>
</div>

<h2>Default</h2>


<div class="ui three cards">
    <div class="ui card">
        <div class="image">
            <img src="/images/template_screenshots/defaultblue_1col.png" alt="Default Blue Template" width="300">
        </div>
        <div class="content">
            <div class="header">
                <h3>One column</h3>
            </div>
            <div class="description">
                <p>Standard clean template, well tested, a good starting point to make your own customisations</p>
            </div>
        </div>
        
        <button class="ui bottom teal attached button js-apply-template" type="button" data-template-name="default_blue_1column">
            <i class="add icon"></i>
            Apply to blog
        </button>
    </div>
    <div class="ui card">
        <div class="image">
            <img src="/images/template_screenshots/defaultblue.png" alt="Default Blue Template" width="300">
        </div>
        <div class="content">
            <div class="header">
                <h3>Two columns - posts on left</h3>
            </div>
            <div class="description">
                <p>Standard clean template, well tested, a good starting point to make your own customisations</p>
            </div>
        </div>
        
        <button class="ui bottom teal attached button js-apply-template" type="button" data-template-name="default_blue_2columns_left">
            <i class="add icon"></i>
            Apply to blog
        </button>
    </div>
    <div class="ui card">
        <div class="image">
            <img src="/images/template_screenshots/defaultblue_2cols_right.png" alt="Default Blue Template" width="300">
        </div>
        <div class="content">
            <div class="header">
                <h3>Two columns - posts on right</h3>
            </div>
            <div class="description">
                <p>Standard clean template, well tested, a good starting point to make your own customisations</p>
            </div>
        </div>
        
        <button class="ui bottom teal attached button js-apply-template" type="button" data-template-name="default_blue_2columns_right">
            <i class="add icon"></i>
            Apply to blog
        </button>
    </div>
        <div class="ui card">
        <div class="image">
            <img src="/images/template_screenshots/defaultblue_3cols_left.png" alt="Default Blue Template" width="300">
        </div>
        <div class="content">
            <div class="header">
                <h3>Three columns - posts on left</h3>
            </div>
            <div class="description">
                <p>Standard clean template, well tested, a good starting point to make your own customisations</p>
            </div>
        </div>
        
        <button class="ui bottom teal attached button js-apply-template" type="button" data-template-name="default_blue_3columns_left">
            <i class="add icon"></i>
            Apply to blog
        </button>
    </div>
        <div class="ui card">
        <div class="image">
            <img src="/images/template_screenshots/defaultblue_3cols_centre.png" alt="Default Blue Template" width="300">
        </div>
        <div class="content">
            <div class="header">
                <h3>Three columns - posts in centre</h3>
            </div>
            <div class="description">
                <p>Standard clean template, well tested, a good starting point to make your own customisations</p>
            </div>
        </div>
        
        <button class="ui bottom teal attached button js-apply-template" type="button" data-template-name="default_blue_3columns_centre">
            <i class="add icon"></i>
            Apply to blog
        </button>
    </div>
        <div class="ui card">
        <div class="image">
            <img src="/images/template_screenshots/defaultblue_3cols_right.png" alt="Default Blue Template" width="300">
        </div>
        <div class="content">
            <div class="header">
                <h3>Three columns - posts on right</h3>
            </div>
            <div class="description">
                <p>Standard clean template, well tested, a good starting point to make your own customisations</p>
            </div>
        </div>
        
        <button class="ui bottom teal attached button js-apply-template" type="button" data-template-name="default_blue_3columns_right">
            <i class="add icon"></i>
            Apply to blog
        </button>
    </div>
</div>

<form method="POST" action="/cms/settings/template/{$blog.id}" id="updateTemplateForm">
    <input type="hidden" name="template_id" value="">
</form>

</form>

<script>
$(".js-apply-template").click(function() {
    var templateName = $(this).data('template-name');
    $("#updateTemplateForm input[name='template_id']").val(templateName);
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

    <input type="button" value="Cancel" class="ui button" name="goback" onclick="window.history.back()" />