$(document).ready(function() {
    $("#cms_main_menu a").click(function() {
        $(this).find("i.icon").attr("class", "spinner loading icon");
    });

    $("a.button, button.button").click(function() {
        $(this).addClass("loading");
    });
});
