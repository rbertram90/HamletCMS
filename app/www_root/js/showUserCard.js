function showUserProfile(elem, clientroot, clientrootblogcms)
{
    if(elem.children("span").children(".user-profile").length == 0)
    {
    
        var userid = elem.children("span").attr("data-userid");
        
        elem.children("span").prepend("<div class='user-profile'><center><img src='" + clientrootblogcms + "/images/ajax-loader.gif' alt='Loading...'/></center></div>");
        
        elem.children("span").children(".user-profile").load("/ajax/usercard/" + userid);
        
    }
    else
    {
        elem.children("span").children(".user-profile").css("display", "block");
    }
}

function hideUserProfile(elem)
{
    elem.children("span").children(".user-profile").css("display", "none");
}

//
// example use
// $(".user-link").mouseenter(showUserProfile($(this), clientroot, clientrootblogcms);
// $(".user-link").mouseleave(hideUserProfile($(this));
//
// markup structure
// <a class="user-link">
//   <span data-userid="70219740">Link-Text</span>
// </a>
//
