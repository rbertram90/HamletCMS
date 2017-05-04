<!--Currently Unused!!!!-->
<h2>Leave a comment</h2>
<form action="default.php?a=<?php echo $post_id;?>" method="post">
	<table width="100%" cellpadding="10">
	<tr><th>Name</th><td><input type="text" name="commentername" size="50" /></td></tr>
	<tr><th>Email</th><td><input type="text" name="commenteremail" size="50" /></td></tr>
	<tr><th>Comment</th><td><textarea name="comment" value="" cols="46" rows="5"></textarea></td></tr>
	</table>
	<input type="hidden" value="<?php echo $post_id; ?>" name="post_id" />
	<input type="hidden" value="<?php echo $key; ?>" name="blog_id" />
	<input type="submit" value="Post Comment" name="post_comment" style="" class="action_button" />
</form>