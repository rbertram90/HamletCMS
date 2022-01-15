<div class="field">
    <label for="post_url">URL</label>
    <p>
        <input type="checkbox" name="field_override_url" id="field_override_url" class="post-data-field" data-key="overrideLink" data-type="checkbox">
        <label for="field_override_url" class="inline">Manually set URL?</label>
    </p>
    <input type="text" name="post_url" id="post_url" required size="50" autocomplete="off" value="{if isset($post)}{$post->link}{/if}" class="post-data-field" data-key="link">
</div>
<script>
{if $post->link_override}
    $("#field_override_url").prop("checked", true);
{/if}

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