<div class="widget">
    <div id="js-owner-profile"><img src="/images/ajax-loader.gif" alt="Loading..."></div>
</div>

{* Currently can only view full profile if they are logged in. Need to do a page with posts written by that user *}

<script>
$.get('{$cms_url}/api/contributors/owner', { blogID: {$blog->id} }, function(owner) {

    var profile = '<div class="ui fluid card"><div class="image">';
        profile += '  <img src="/hamlet/avatars/thumbs/' + owner.profile_picture + '">';
        profile += '</div><div class="content">';
        profile += '<a class="header" href="/cms/account/user/' + owner.id + '">' + owner.name + ' ' + owner.surname + '</a>';
        profile += '<div class="meta">' + owner.username + '</div>';
        profile += '<div class="description">' + owner.description + '</div></div>';
        profile += '<div class="extra content"><span class="right floated"><i class="venus mars icon"></i> ' + owner.gender + '</span>';
        profile += '<span><i class="map marker alternate icon"></i> ' + owner.location + '</span></div>';
        profile += '</div>';

    $("#js-owner-profile").html(profile);
});
</script>