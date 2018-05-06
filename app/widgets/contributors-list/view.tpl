<h2>{$heading}</h2>
<div id="js-contributor-list" class="ui {$columns} cards"><img src="/images/ajax-loader.gif" alt="Loading..."></div>

{* todo choose the number of columns via settings *}

<script>
$.get('{$cms_url}/api/contributors', { blogID: {$blog.id} }, function(contributors) {
    var profiles = "";

    for(var i = 0; i < contributors.length; i++) {

        var contributor = contributors[i];

        profiles += '<div class="ui card"><div class="image">';
        profiles += '  <img src="/avatars/thumbs/' + contributor.profile_picture + '">';
        profiles += '</div><div class="content">';
        profiles += '<a class="header" href="/cms/account/user/' + contributor.id + '">' + contributor.name + ' ' + contributor.surname + '</a>';
        profiles += '<div class="meta">' + contributor.username + '</div>';
        profiles += '<div class="description">' + contributor.description + '</div></div>';
        profiles += '<div class="extra content"><span class="right floated"><i class="venus mars icon"></i> ' + contributor.gender + '</span>';
        profiles += '<span><i class="map marker alternate icon"></i> ' + contributor.location + '</span></div>';
        profiles += '</div>';

    }

    $("#js-contributor-list").html(profiles);
});
</script>