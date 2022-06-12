{* todo - add option to reset template to default *}
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <h2>Image sizes</h2>
            <div class="ui icon info message">
                <i class="info circle icon"></i>
                <div class="content">
                    <p>When uploading an image to HamletCMS, several different versions are also saved. These should be smaller images designed to be used on pages which list multiple posts, to prevent people viewing your blog from having to download large image files.</p>
                </div>
            </div>

            <div id="return_messages"></div>

            <button class="ui teal button add-size-button" data-no-spinner="true">Add new size</button>

            <form method="POST" class="ui form" id="post_settings_form">
                {foreach $config.imagestyles as $name => $size}
                <div class="ui segment" id="{$name}_wrapper">
                    <div class="ui grid">
                        <div class="three columns row">
                            <div class="column">
                                <h3>{$name}</h3>
                                <div class="field">
                                    <label for="{$name}_image_width">Width (px)</label>
                                    <input type="number" id="{$name}_image_width" name="{$name}_image_width" value="{$size.w}">
                                </div>
                            </div>
                            <div class="column">
                                <h3>&nbsp;</h3>
                                <div class="field">
                                    <label for="{$name}_image_height">Height (px)</label>
                                    <input type="number" id="{$name}_image_height" name="{$name}_image_height" value="{$size.h}">
                                </div>
                            </div>
                            <div class="column">
                                {if $name == 'square'}
                                    {* Cannot delete this one *}
                                {else}
                                    <button class="ui red button delete-size-button" data-name="{$name}" data-no-spinner="true" style="position:absolute; bottom: 1px;">Delete</button>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}

                <div class="field">
                    <label for="default_image_size">Default</label>
                    <select name="default_image_size" id="default_image_size" required>
                        <option value="">- select - </option>
                        {foreach $config.imagestyles as $name => $size}
                            {if $name === $config['defaultsize']}
                                <option value="{$name}" selected>{$name}</option>
                            {else}
                                <option value="{$name}">{$name}</option>
                            {/if}
                            
                        {/foreach}
                    </select>
                    <p style="margin-top: 2px; color:#666; font-size:0.8em;">Used for manage files page.</p>
                    <script>
                        $("#default_image_size").dropdown();
                    </script>
                </div>


                <button class="ui teal button" type="submit">Save</button>
            </form>

        </div>
    </div>
</div>

{* Add Widget Form *}
<div class="ui modal" id="add_size_modal">
    <i class="close icon"></i>
    <div class="header">
        Add image size
    </div>
    <div class="content">
        <form action="/cms/files/addsize/{$blog->id}" method="post" class="ui form" id="create_size_form">
            <div class="field">
                <label for="new_image_name">Name</label>
                <input type="text" id="new_image_name" name="new_image_name" value="" required>
                <p style="margin-top: 2px; color:#666; font-size:0.8em;">Name should be lowercase and contain only letters and numbers.</p>
            </div>
            <div class="two fields">
                <div class="field">
                    <label for="new_image_width">Width (px)</label>
                    <input type="number" id="new_image_width" name="new_image_width" value="" required>
                </div>
                <div class="field">
                    <label for="new_image_height">Height (px)</label>
                    <input type="number" id="new_image_height" name="new_image_height" value="" required>
                </div>
            </div>

            <input type="submit" style="display:none" id="real_create_size_button">
        </form>
    </div>
    <div class="actions">
        <button class="ui teal button" id="fake_create_size_button" data-no-spinner="true">Create</button>
        <div class="ui deny button">
            Close
        </div>
    </div>
</div>

<div class="ui basic modal" id="delete_size_modal">
  <div class="ui icon huge header">
    <i class="trash alternate outline icon"></i>
    Delete image size
  </div>
  <div class="content" style="text-align:center;">
    <p>Are you sure you want to delete this size setting? All images for this size will be removed.</p>
  </div>
  <div class="actions" style="text-align:center;">
    <a class="big ui green ok inverted button" id="confirm_delete_size_button" data-no-spinner="true">
      <i class="checkmark icon"></i>
      Delete
    </a>
    <div class="big ui red basic cancel inverted button">
      <i class="remove icon"></i>
      Nevermind
    </div>
  </div>
</div>

<script>
    $(".add-size-button").click(function(event) {
        event.preventDefault();
        var $modal = $("#add_size_modal");
        $modal.modal('show');
    });

    $("#fake_create_size_button").click(function(event) {
        event.preventDefault();
        $("#real_create_size_button").click(); // one way to ensure browser validation still runs
    });


    $(".delete-size-button").click(function(event) {
        event.preventDefault();

        var sizeName = $(this).data("name");
        var $modal = $("#delete_size_modal");

        $("#confirm_delete_size_button").data('name', sizeName);

        $modal.modal('show');
    });
    $("#confirm_delete_size_button").click(function(event) {
        event.preventDefault();
        var sizeName = $(this).data("name");

        $.ajax({
            url: '/api/files/removesize',
            type: 'post',
            data: {
                sizeName: sizeName,
                blogID: {$blog->id}
            }
        }).done(function (data, textStatus, jqXHR) {
            $("#delete_size_modal").modal('hide');
            $("#" + sizeName + "_wrapper").remove();
            $("#return_messages").html('<p class="ui success message">Image size deleted</p>');

        }).fail(function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);
            $("#return_messages").html('<p class="ui error message">' + data.errorMessage + '</p>');
        });
    });
</script>