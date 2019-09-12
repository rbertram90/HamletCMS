<div class="field">
    <label for="draft">Action to take <a href="/" onclick="alert('Post to Blog: This post will be live on your blog for anyone that can see you blog to read. Blog security settings can be changed in the settings section.\n\nSave as Draft: The post will be saved for further editing later and will not be visible to your readers.'); return false;">[?]</a></label>
    <select name="draft" id="draft" class="ui dropdown"  class="post-data-field">
        <option value="0">Post to Blog</option>
        <option value="1">Save as Draft</option>
    </select>
</div>

<script>
{if $mode == 'edit'}
    $("#draft").val({$post->draft});
{/if}
$("#draft").dropdown();
</script>