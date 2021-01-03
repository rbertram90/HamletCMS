<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", {$blog->name}, "/cms/settings/menu/{$blog->id}", 'Settings'), 'Comment settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Comment settings', 'comments', {$blog->name})}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <form method="POST" class="ui form" id="post_settings_form">
                <div class="field">
                    <label for="comments_setting">Setting 1</label>
                    <input type="number" value="{$settings.something}" name="comments_setting" id="comments_setting" placeholder="">
                </div>
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="comment_moderation_enabled" id="comment_moderation_enabled">
                        <label for="comment_moderation_enabled">Enable comment moderation</label>
                    </div>
                </div>
                <button class="ui teal labeled icon button"><i class="save icon"></i> Save</button>
            </form>
        </div>
    </div>
</div>