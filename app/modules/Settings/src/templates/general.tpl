<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(["/cms/blog/overview/{$blog->id}", "{$blog->name}", "/cms/settings/menu/{$blog->id}", 'Settings'], 'General Settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('General settings', 'sliders horizontal', "{$blog->name}")}

            <form method="POST" class="ui form" enctype="multipart/form-data">
                
                <div class="field">
                    <label for="fld_blogname">Blog Name</label>
                    <input type="text" value="{$blog->name}" name="fld_blogname" required>
                </div>
                
                <div class="field">
                    <label for="fld_blogdesc">Description</label>
                    <textarea name="fld_blogdesc">{$blog->description}</textarea>
                </div>

                <div class="field">
                    <label for="fld_logo">Logo</label>
                    {if strlen("{$blog->logo}") && file_exists("{$smarty.const.SERVER_PUBLIC_PATH}/blogdata/{$blog->id}/{$blog->logo}")}
                        <img src="/blogdata/{$blog->id}/{$blog->logo}" alt="logo" style="max-width: 150px; max-height: 150px;">
                    {/if}
                    <p><small>Max upload size = 100 KB</small></p>
                    <input type="file" name="fld_logo" id="fld_logo">
                </div>

                <div class="field">
                    <label for="fld_favicon">Favicon</label>
                    {if strlen("{$blog->icon}") && file_exists("{$smarty.const.SERVER_PUBLIC_PATH}/blogdata/{$blog->id}/{$blog->icon}")}
                        <img src="/blogdata/{$blog->id}/{$blog->icon}" alt="icon" style="max-width: 150px; max-height: 150px;">
                    {/if}
                    <p><small>Max upload size = 50 KB, for best results this should be a square image</small></p>
                    <input type="file" name="fld_favicon" class="fld_favicon">
                </div>

                <div class="field">
                    <label for="fld_domain">Custom domain<br>
                    <small><em>Requires server configuration to work, see: <a href="https://github.com/rbertram90/blog_cms/wiki/Applying-custom-blog-domain-names" target="_blank">this wiki article</a></em></small></label>
                    <input type="text" value="{$blog->domain}" name="fld_domain" placeholder="(default)">
                    <small>Include http(s):// but no trailing slash.</small>
                </div>
                
                <div class="field">
                    <label for="fld_category">Category</label>
                    <select id="fld_category" name="fld_category" class="semantic-dropdown">
                        {foreach from=$categorylist item=category}
                            <option value="{$category}">{ucfirst($category)}</option>
                        {/foreach}
                    </select>
                    <script type="text/javascript">
                        // Set default
                        $("#fld_category").val("{$blog->category}");
                    </script>
                </div>
                
                <div class="field">
                    <label for="fld_blogsecurity">Who should be able to read your blog?</label>
                    <select id="fld_blogsecurity" name="fld_blogsecurity" class="semantic-dropdown">
                        <option value="anon">Everyone</option>
                        <option value="members">Logged In Members</option>
                        <option value="friends">Your Friends</option>
                        <option value="private">Private (Just You)</option>
                    </select>
                    <script type="text/javascript">
                        // Set default
                        $("#fld_blogsecurity").val("{$blog->visibility}");
                    </script>

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