<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(["/cms/blog/overview/{$blog.id}", "{$blog.name}", "/cms/settings/menu/{$blog.id}", 'Settings'], 'General Settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('General settings', 'sliders horizontal', "{$blog.name}")}

            <form method="POST" class="ui form">
                
            <!-- to think about - how much of the settings do we want in UI and how much in config.json?
                it would be MUCH quick via. json - allowing time to be used in actually implementing the setting
                however, everyone likes a settings screen...?
            -->
           
                <div class="field">
                    <label for="fld_allow_comments">Allow comments</label>
                    <input type="text" value="" name="fld_allow_comments" />
                </div>

                <div class="field">
                    <label for="fld_allow_comments">Enable reCaptcha</label>
                    <input type="text" value="" name="fld_allow_comments" />
                </div>
                
                <div class="field">
                    <label for="fld_allow_comments">Maximum comment length</label>
                    <input type="text" value="" name="fld_allow_comments" />
                </div>

                <div class="field">
                    <label for="fld_allow_comments">Maximum comment submissions per user per minute</label>
                    <input type="text" value="" name="fld_allow_comments" />
                </div>

                <div class="field">
                    <label for="fld_allow_comments">Maximum total comment submissions per minute</label>
                    <!-- incase of distributed attack? -->
                    <input type="text" value="" name="fld_allow_comments" />
                </div>

                <script>
                    // Apply semantic UI dropdown
                    $(".semantic-dropdown").dropdown();
                </script>

                <input type="submit" class="ui button floated right teal" value="Update" />
                <input type="button" value="Cancel" class="ui button floated right" name="goback" onclick="window.history.back()" />

            </form>
        </div>
    </div>
    
</div>