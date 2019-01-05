<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", {$blog->name}, "/cms/settings/menu/{$blog->id}", 'Settings'), 'Post settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Post settings', 'sliders horizontal', {$blog->name})}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <form method="POST" class="ui form" id="post_settings_form">
                <div class="ui grid">
                    <div class="one column row">
                        <div class="column">
                            <h2>Templates</h2>
                            <div class="ui message">
                                <p>These template use the Smarty templating engine, for more information see: <a href="https://www.smarty.net/docs/en/smarty.for.designers.tpl" target="_blank">Smarty for template designers</a>.</p>
                                <p>Consult the <a href="https://github.com/rbertram90/blog_cms/wiki/Writing-custom-post-teaser-templates" target="_blank">Blog CMS wiki</a> for further help and a list of <a href="https://github.com/rbertram90/blog_cms/wiki/Writing-custom-post-teaser-templates" target="_blank">available variables</a> for this template.</p>
                            </div>
                            <div class="ui top attached tabular menu templates">
                                <a class="active item" data-tab="first">Teaser</a>
                                <a class="item" data-tab="second">Full</a>
                            </div>
                            <div class="ui bottom attached active tab segment" data-tab="first">
                                <div class="field">
                                    <textarea id="ace_edit_view" name="ace_edit_view" rows="20" style="font-family: monospace;">{$postTemplate}</textarea>
                                    <textarea name="fld_post_template" style="display: none;">{$postTemplate}</textarea>
                                    <script>
                                        var ace_editor = ace.edit("ace_edit_view");
                                        ace_editor.setTheme("ace/theme/textmate");
                                        ace_editor.session.setMode("ace/mode/smarty");
                                        $(".ace_editor").height('50vh');
                                        var textarea = $('textarea[name="fld_post_template"]');
                                        ace_editor.getSession().on("change", function () {
                                            textarea.val(ace_editor.getSession().getValue());
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="ui bottom attached tab segment" data-tab="second">
                                <div class="field">
                                    <textarea id="ace_edit_view_full" name="ace_edit_view_full" rows="20" style="font-family: monospace;">{$postFullTemplate}</textarea>
                                    <textarea name="fld_post_full_template" style="display: none;">{$postFullTemplate}</textarea>
                                    <script>
                                        var ace_editor_full = ace.edit("ace_edit_view_full");
                                        ace_editor_full.setTheme("ace/theme/textmate");
                                        ace_editor_full.session.setMode("ace/mode/smarty");
                                        $(".ace_editor").height('50vh');
                                        var textarea_full = $('textarea[name="fld_post_full_template"]');
                                        ace_editor_full.getSession().on("change", function () {
                                            textarea_full.val(ace_editor_full.getSession().getValue());
                                        });
                                    </script>
                                </div>
                            </div>
                            <script>
                                $('.templates .item').tab();
                            </script>                            
                        </div>
                    </div>
                    <div class="one column row">
                        <div class="column">
                            <h2>Other settings</h2>
                        </div>
                    </div> 
                    <div class="two column row">
                        
                        <div class="column">
                            <div class="field">
                                <label for="fld_showtags">Show Post Tags?</label>
                                <select id="fld_showtags" name="fld_showtags" class="ui dropdown">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            
                            <div class="field">
                                <label for="fld_shownumcomments">Show Number of Comments</label>
                                <select id="fld_shownumcomments" name="fld_shownumcomments" class="ui dropdown">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="fld_postsummarylength">Length of Post Summary (Characters)</label>
                                <input type="text" value="{$postConfig.postsummarylength}" name="fld_postsummarylength"  placeholder="Number of Characters" />
                            </div>
                        </div>
                        
                        <div class="column">
                            <div class="field">
                                <label for="fld_showsocialicons">Show 'Share to Social Media' Icons</label>
                                <select id="fld_showsocialicons" name="fld_showsocialicons" class="ui dropdown">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            
                            <div class="field">
                                <label for="fld_postsperpage">Number of Posts Per Page</label>
                                <input type="text" value="{$postConfig.postsperpage}" name="fld_postsperpage" />
                            </div>
                        </div>
                        
                    </div>
                    <div class="one column row">
                        <div class="column">
                            <input type="button" value="Cancel" name="goback" class="right floated ui button" onclick="window.history.back()" />
                            <input type="submit" value="Update" class="right floated teal ui button" />
                        </div>
                    </div>
                </div>
                
                    <!--
                            <label for="fld_commentapprove">Who can comment</label>
                            <select id="fld_commentapprove" name="fld_commentapprove">
                                <option>Anyone</option>
                                <option>RBwebdesigns Users</option>
                                <option>Blog Contributors</option>
                            </select>

                            <label for="fld_commentapprove">Comment Approval <br/><i style="font-weight:normal;">Select if you want to approve comments before they are displayed on your blog, this can help reduce spam.</i></label>
                            <select id="fld_commentapprove" name="fld_commentapprove">
                                <option value="1">Display Automatically (Default)</option>
                                <option value="0">Manual Approve</option>
                            </select>
                        -->                
                            
                <script>
                    // Default Values in Dropdowns
                    {if array_key_exists('showtags', $postConfig)}
                        $("#fld_showtags").val("{$postConfig.showtags}");
                    {/if}

                    {if array_key_exists('showsocialicons', $postConfig)}
                        $("#fld_showsocialicons").val("{$postConfig.showsocialicons}");
                    {/if}

                    {if array_key_exists('shownumcomments', $postConfig)}
                        $("#fld_shownumcomments").val("{$postConfig.shownumcomments}");
                    {/if}

                    {*$("#fld_commentapprove").val("$postConfig.allowcomments");*}

                    $('select.dropdown').dropdown();
                </script>
            </form>
        </div>
    </div>
    
</div>