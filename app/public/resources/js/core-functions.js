// Redirect the page, confirming on exit
function redirect(path) {
    if(confirm("Are you sure you want to leave the page, any data will be lost.")) {
        window.location=path;
    }
}

/* This script and many more are available free online at
The JavaScript Source :: http://javascript.internet.com
Created by: Philip Myers :: http://virtualipod.tripod.com/bookmark.html */
function bookmark(url,title){
    if ((navigator.appName == "Microsoft Internet Explorer") && (parseInt(navigator.appVersion) >= 4)) {
        window.external.AddFavorite(url,title);
    }
    else if (navigator.appName == "Netscape") window.sidebar.addPanel(title,url,"");
    else alert("Press CTRL-D (Netscape) or CTRL-T (Opera) to bookmark");
}

function confirmdelete() { 
    return confirm("Are you sure you want to delete?");   
}

function showmenu(elmnt) {
    document.getElementById(elmnt).style.visibility="visible";
}

function hidemenu(elmnt) {
    document.getElementById(elmnt).style.visibility="hidden";
}

function slideDown(elem,time) {
    $(elem).slideDown(time);
};
function slideUp(elem,time) {
    $(elem).slideUp(time);
};

function debug_trace(message) {
    $("#debug_trace").html(message).fadeIn(500);
}