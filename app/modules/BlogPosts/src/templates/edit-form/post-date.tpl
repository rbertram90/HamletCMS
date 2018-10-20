<div class="field">
    <label for="post_date">Schedule post
        <a href="/" onclick="alert('Set the date and time for this post to show on your blog.'); return false;">[?]</a>
    </label>
    <div class="ui calendar" id="post_date_wrapper">
        <div class="ui input left icon">
            <i class="calendar icon"></i>
            <input type="text" name="post_date" id="post_date" placeholder="Date/Time" value="{$postdate}">
        </div>
    </div>
    <script>$('#post_date_wrapper').calendar();</script>
</div>