<div class="field">
    <label for="allow_comment">Allow comments <a href="/" onclick="alert('This option will allow logged in users to post comments on your blog posts. You can control whether these are shown automatically in the blog settings.'); return false;">[?]</a></label>
    <select name="allow_comment" id="allow_comment" class="ui dropdown">
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<script>
{if $mode == 'edit'}
    $("#allow_comment").val({$post.allowcomments});
{/if}
</script>