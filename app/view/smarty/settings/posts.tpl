<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/blog/overview/{$blog.id}", {$blog.name}, "/settings/menu/{$blog.id}", 'Settings'), 'Post Settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Post Settings', 'pages_gear.png', {$blog.name})}

            <form action="/settings/posts/{$blog.id}" method="POST" class="ui form">
                
                <div class="ui grid">
                    <div class="two column row">
                        
                        <div class="column">
                            <div class="field">
                                <label for="fld_dateprefix">Date/Time Prefix</label>
                                <input type="text" name="fld_dateprefix" id="fld_dateprefix" placeholder="blank">
                            </div>
                            
                            <div class="field">
                                <label for="fld_dateformat">Date Format</label>
                                <select id="fld_dateformat" name="fld_dateformat">
                                    <option value="Y-m-d">1998-03-27 (Default)</option>
                                    <option value="d/m/Y">27/03/1998</option>
                                    <option value="d/m">27/03</option>
                                    <option value="d.m.y">27.03.98</option>
                                    <option value="d.m">27.03</option>
                                    <option value="l jS F">Friday 27th March</option>
                                    <option value="j F">27 March</option>
                                    <option value="j F Y">27 March 1998</option>
                                    <option value="j M">27 Mar</option>
                                    <option value="j M Y">27 Mar 1998</option>
                                </select>
                            </div>
                            
                            <div class="field">
                                <label for="fld_datelocation">Date Location</label>
                                <select id="fld_datelocation" name="fld_datelocation">
                                    <option value="footer">Post Footer (Default)</option>
                                    <option value="title">Above Post Title</option>
                                    <option value="hidden">Don't Show</option>
                                </select>
                            </div>

                        </div>
                        
                        <div class="column">
                            <div class="field">
                                <label for="fld_dateseperator">Date/Time Seperator</label>
                                <input type="text" name="fld_dateseperator" id="fld_dateseperator">
                            </div>
                            
                            <div class="field">
                                <label for="fld_timeformat">Time Format</label>
                                <select id="fld_timeformat" name="fld_timeformat">
                                    <option value="H:i:s">15:21:00 (Default)</option>
                                    <option value="H:i">15:21</option>
                                    <option value="g:ia">3:21am</option>
                                </select>
                            </div>
                            
                            <div class="field">
                                <label for="fld_timelocation">Time Location</label>
                                <select id="fld_timelocation" name="fld_timelocation">
                                    <option value="footer">Post Footer (Default)</option>
                                    <option value="title">Above Post Title</option>
                                    <option value="hidden">Don't Show</option>
                                </select>
                            </div>
                        </div>                  
                    </div>
                    <div class="one column row">
                        <div class="column">
                            <div class="field">
                                <label><i>Date/Time Preview</i></label>
                                <div id="postfooter-preview" style="background-color:#eee; border:1px solid #ccc; padding:5px; margin-bottom:16px;">Preview</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="two column row">
                        
                        <div class="column">
                            <div class="field">
                                <label for="fld_showtags">Show Post Tags?</label>
                                <select id="fld_showtags" name="fld_showtags">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            
                            <div class="field">
                                <label for="fld_shownumcomments">Show Number of Comments</label>
                                <select id="fld_shownumcomments" name="fld_shownumcomments">
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
                                <select id="fld_showsocialicons" name="fld_showsocialicons">
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
                    {if array_key_exists('timeformat', $postConfig)}
                        $("#fld_timeformat").val("{$postConfig.timeformat}");
                    {/if}
                    {if array_key_exists('dateformat', $postConfig)}
                        $("#fld_dateformat").val("{$postConfig.dateformat}");
                    {/if}

                    {if array_key_exists('showtags', $postConfig)}
                        $("#fld_showtags").val("{$postConfig.showtags}");
                    {/if}
                    {if array_key_exists('showsocialicons', $postConfig)}
                        $("#fld_showsocialicons").val("{$postConfig.showsocialicons}");
                    {/if}
                    {if array_key_exists('shownumcomments', $postConfig)}
                        $("#fld_shownumcomments").val("{$postConfig.shownumcomments}");
                    {/if}

                    {if array_key_exists('datelocation', $postConfig)}
                        $("#fld_datelocation").val("{$postConfig.datelocation}");
                    {/if}
                    {if array_key_exists('timelocation', $postConfig)}
                        $("#fld_timelocation").val("{$postConfig.timelocation}");
                    {/if}

                    {if array_key_exists('dateseperator', $postConfig)}
                        $("#fld_dateseperator").val("{$postConfig.dateseperator}");
                    {else}
                        $("#fld_dateseperator").val(" at ");
                    {/if}

                    {if array_key_exists('dateprefix', $postConfig)}
                        $("#fld_dateprefix").val("{$postConfig.dateprefix}");
                    {else}
                        $("#fld_dateprefix").val("Posted on: ");
                    {/if}

                    {*$("#fld_commentapprove").val("$postConfig.allowcomments");*}

                    function format2DigitNumber(number) {
                        if(number < 10) {
                            return '0' + number;
                        } else {
                            return number;
                        }
                    }

                    var updatePreview = function() {

                        var res = "";
                        var d = new Date();
                        var months = ['January','Febuary','March','April','May','June','July','August','September','October','November','December'];
                        var shortmonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        var days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

                        if($("#fld_datelocation").val() == 'hidden') {
                            // Don't show
                        }
                        else {
                            res+= $("#fld_dateprefix").val();

                            switch($("#fld_dateformat").val()) {
                                case "Y-m-d":
                                    var lmonth = format2DigitNumber(d.getMonth()+1);
                                    res+= d.getFullYear() + "-" + lmonth +  "-" + d.getDate();
                                    break;
                                case "d/m/Y":
                                    res+= d.getDate() + "/" + (d.getMonth()+1) + "/" + d.getFullYear();
                                    break;
                                case "d/m":
                                    res+= d.getDate() + "/" + (d.getMonth()+1);
                                    break;
                                case "d.m.y":
                                    res+= d.getDate() + "." + (d.getMonth()+1) + "." + d.getFullYear().toString().substring(2, 4);
                                    break;
                                case "d.m":
                                    res+= d.getDate() + "." + (d.getMonth()+1);
                                    break;
                                case "l jS F":
                                    if(d.getDate() == 1 || d.getDate() == 21 || d.getDate() == 31) prefix = "st";
                                    else if(d.getDate() == 2 || d.getDate() == 22) prefix = "nd";
                                    else if(d.getDate() == 3 || d.getDate() == 23) prefix = "rd";
                                    else prefix = "th";
                                    res+= days[d.getDay()] + " " + d.getDate() + prefix + " " + months[d.getMonth()];
                                    break;
                                case "j F":
                                    res+= d.getDate() + " " + months[d.getMonth()];
                                    break;
                                case "j F Y":
                                    res+= d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear();
                                    break;
                                case "j M":
                                    res+= d.getDate() + " " + shortmonths[d.getMonth()];
                                    break;
                                case "j M Y":
                                    res+= d.getDate() + " " + shortmonths[d.getMonth()] + " " + d.getFullYear();
                                    break;
                            }

                        }

                        if($("#fld_timelocation").val() == 'hidden') {
                            // Do nothing More...

                        } else {

                            res+= $("#fld_dateseperator").val();

                            switch($("#fld_timeformat").val()) {
                                case "H:i:s":
                                    res+= d.getHours() + ":" + format2DigitNumber(d.getMinutes()) + ":" + format2DigitNumber(d.getSeconds());
                                    break;
                                case "H:i":
                                    res+= d.getHours() + ":" + format2DigitNumber(d.getMinutes());
                                    break;
                                case "g:ia":
                                    if(d.getHours() > 12) {
                                        res+= (d.getHours()-12) + ":" + format2DigitNumber(d.getMinutes()) + "PM";
                                    } else {
                                        res+= d.getHours() + ":" + format2DigitNumber(d.getMinutes()) + "AM";
                                    }
                                    break;
                            }
                        }

                        $("#postfooter-preview").html(res);
                    };

                    updatePreview();

                    $("#fld_timeformat").change(updatePreview);
                    $("#fld_dateformat").change(updatePreview);
                    $("#fld_dateprefix").change(updatePreview);
                    $("#fld_dateseperator").change(updatePreview);
                    $("#fld_datelocation").change(updatePreview);
                    $("#fld_timelocation").change(updatePreview);
                    // $("#fld_showtags").change(updatePreview);
                </script>
            </form>
        </div>
    </div>
    
</div>