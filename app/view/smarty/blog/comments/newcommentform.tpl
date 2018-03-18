{if $userAuthenticated}
    <div class="comment-form">
        <form action="/blogs/{$post.blog_id}/posts/{$post.id}/addcomment" method="POST">
            
            <label for="fld_comment">Comment</label>
            <textarea name="fld_comment"></textarea>

            <div class="push-right">
                <input type="submit" name="fld_submitcomment" value="Add Comment" />
            </div>
        </form>
    </div>
{/if}