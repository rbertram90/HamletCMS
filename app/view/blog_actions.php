<?php if($DATA['user_is_contributor']): ?>
	<a href="/" class="action_button">Dashboard</a>
	<a href="/posts/<?=$DATA['blog_key']?>/new" class="action_button">Create New Post</a>
	<a href="/posts/<?=$DATA['blog_key']?>" class="action_button">Manage Posts</a>
<?php elseif($gbLoggedIn): ?>
	<a href="/" class="action_button">Dashboard</a>

	<?php if($DATA['is_favourite']): ?>
		<a href="#" onclick="removeFavourite(<?=$DATA['blog_key']?>); return false;" class="action_button btn_green" title="Click to Remove from favourites list." id="btn_favourite">Added as Favourite</a>
	<?php else: ?>
		<a href="#" onclick="addFavourite(<?=$DATA['blog_key']?>); return false;" class="action_button" title="Click to Add to favourites list." id="btn_favourite">Not in Favourites</a>
	<?php endif; ?>

<?php endif; ?>