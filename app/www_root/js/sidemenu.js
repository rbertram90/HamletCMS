window.showSideMenu = function () {
    $("#wrapper").addClass("desktop");
    $("#wrapper").removeClass("tablet");
    localStorage.setItem('sidemenustate', 'desktop');    
};

window.hideSideMenu = function () {
    $("#wrapper").addClass("tablet");
    $("#wrapper").removeClass("desktop");
    localStorage.setItem('sidemenustate', 'tablet');
};

$(document).ready(function () {
    var menustate = localStorage.getItem('sidemenustate');
    if (menustate) {
        if (menustate === 'desktop') {
            $("#wrapper").addClass("desktop");
        } else {
            $("#wrapper").addClass("tablet");
        }
    } else {
        $("#wrapper").addClass("desktop");
    }
});