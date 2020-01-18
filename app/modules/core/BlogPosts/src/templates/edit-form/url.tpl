<div class="field">
    <label for="post_url">URL</label>
    <p><input type="checkbox" name="field_override_url" id="field_override_url" class="post-data-field" data-key="overrideLink" data-type="checkbox"> Manually set URL?</p>
    <input type="text" name="post_url" id="post_url" required size="50" autocomplete="off" value="{$post->link}" class="post-data-field" data-key="link">
</div>
<script>
    $("#field_override_url").change(function() {
        if ($(this).is(':checked')) {
            $("#post_url").prop('disabled', false);
        }
        else {
            $("#post_url").prop('disabled', true);
        }
    });

    // Initial state
    if (!$('#field_override_url').is(':checked')) {
        $("#post_url").prop('disabled', true);
    }
</script>