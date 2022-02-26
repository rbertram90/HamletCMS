<div class="ui grid">
    <div class="one column row">
        <div class="column">
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
                    {if strlen("{$blog->logo}") && file_exists("{$smarty.const.SERVER_PATH_BLOGS}/{$blog->id}/{$blog->logo}")}
                        <img src="{$blog->resourcePath()}/{$blog->logo}" alt="logo" style="max-width: 150px; max-height: 150px;">
                    {/if}
                    <p><small>Max upload size = 100 KB</small></p>
                    <input type="file" name="fld_logo" id="fld_logo">
                </div>

                <div class="field">
                    <label for="fld_favicon">Favicon</label>
                    {if strlen("{$blog->icon}") && file_exists("{$smarty.const.SERVER_PATH_BLOGS}/{$blog->id}/{$blog->icon}")}
                        <img src="{$blog->resourcePath()}/{$blog->icon}" alt="icon" style="max-width: 150px; max-height: 150px;">
                    {/if}
                    <p><small>Max upload size = 50 KB, for best results this should be a square image</small></p>
                    <input type="file" name="fld_favicon" class="fld_favicon">
                </div>

                <div class="field">
                    <label for="fld_domain">Custom domain<br>
                    <small><em>Requires server configuration to work, see: <a href="https://github.com/rbertram90/blog_cms/wiki/Applying-custom-blog-domain-names" target="_blank">this wiki article</a></em></small></label>
                    <input type="text" value="{$blog->domain}" name="fld_domain" id="fld_domain" placeholder="(default)">
                    <small>Include http(s):// but no trailing slash.</small>
                </div>

                <div class="field">
                    <label for="fld_homepage_type">Homepage content</label>
                    <select name="fld_homepage_type" id="fld_homepage_type">
                        <option value="posts">Recent posts (default)</option>
                        <option value="single">Select a post...</option>
                        <option value="tags">Tag lists</option>
                    </select>
                </div>

                <div class="field" id="home_page_wrapper" style="display:none;">
                    <label for="homepage">Post to show</label>
                    <div class="ui search selection dropdown" id="homepage">
                        <input type="hidden" value="" name="fld_homepage_post_id" id="fld_homepage_post_id">
                        <i class="dropdown icon"></i>
                        <input class="search" type="text" id="post_search_text">
                        <div class="text"></div>
                    </div>
                </div>

                <div class="ui segment field" id="tag_list_wrapper" style="display:none;">
                    <button class="ui labeled icon button" type="button" id="add_tag_section" data-no-spinner="true"><i class="plus icon"></i> Add tag</button>
                    <div id="tag_sections_view" class="ui segments"></div>
                    <input type="hidden" id="fld_tag_sections" name="fld_tag_sections" value="">
                </div>
                <style>#tag_sections_view .red.icon.button { margin-left: 6px; }</style>

                <script>
                    var getTagDropdown = function () {
                        var allTags = {$tagList};
                        var outer = document.createElement('div');
                        outer.className = 'ui segment';
                        var dropdown = document.createElement('select');
                        dropdown.className = 'tag_selection';
                        dropdown.addEventListener('change', refreshTagValues);

                        for (var t = 0; t < allTags.length; t++) {
                            var option = document.createElement('option');
                            option.text = allTags[t];
                            option.value = allTags[t];
                            dropdown.appendChild(option);
                        }

                        var remove = document.createElement('button');
                        remove.className = 'ui red icon button';
                        remove.innerHTML = '<i class="delete icon"></i>';
                        remove.type = 'button';
                        remove.addEventListener('click', function(e) {
                            this.parentElement.remove();
                            refreshTagValues();
                            e.preventDefault();
                        });

                        outer.appendChild(dropdown);
                        outer.appendChild(remove);
                        return outer;
                    };

                    var refreshTagValues = function () {
                        var tags = [];
                        $(".tag_selection").find("select").each(function() {
                            tags.push(this.value);
                        });
                        console.log(typeof JSON.stringify(tags));
                        $("#fld_tag_sections").attr('value', JSON.stringify(tags));
                    };

                    $("#homepage").dropdown({
                        placeholder: 'Search for post',
                        minCharacters: 2,
                        apiSettings: {
                            url: '/api/posts/search?blogID={$blog->id}&q={ldelim}query{rdelim}'
                        }
                    });

                    {if $config.homepage_type}
                        $("#fld_homepage_type").val('{$config.homepage_type}');
                        $("#fld_homepage_post_id").val('{$config.homepage_post_id}');
                        $("#post_search_text").val('[Post #{$config.homepage_post_id}]');
                    {/if}

                    $('#fld_homepage_type').dropdown();

                    var changeHomepageTypeView = function() {
                        switch ($("#fld_homepage_type").val()) {
                            case 'posts':
                                $("#home_page_wrapper").hide();
                                $("#tag_list_wrapper").hide();
                                break;
                            case 'single':
                                $("#home_page_wrapper").show();
                                $("#tag_list_wrapper").hide();
                                break;
                            case 'tags':
                                $("#home_page_wrapper").hide();
                                $("#tag_list_wrapper").show();
                                break;
                        }
                    };

                    $("#fld_homepage_type").change(changeHomepageTypeView);
                    changeHomepageTypeView();

                    $("#add_tag_section").click(function() {
                        $("#tag_sections_view").append(getTagDropdown());
                        $(".tag_selection").dropdown();
                        refreshTagValues();
                    }); 

                    if ($("#fld_post_as_homepage").is(':checked')) {
                        $("#home_page_wrapper").show();
                    }

                    {if $config.homepage_tag_list}
                        var currenttags = {html_entity_decode($config.homepage_tag_list)};
                        for (var ct = 0; ct < currenttags.length; ct++) {
                            var dd = getTagDropdown();
                            $("#tag_sections_view").append(dd);
                            $(dd).find('select').val(currenttags[ct]);
                        }
                        $(".tag_selection").dropdown();
                        refreshTagValues();
                    {/if}
                </script>

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