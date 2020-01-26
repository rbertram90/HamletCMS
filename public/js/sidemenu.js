$(document).ready(function() {
    $("#cms_main_menu a").click(function() {
        $(this).find("i.icon").attr("class", "spinner loading icon");
    });

    $("a.button, button.button").click(function() {
        if (!$(this).data('no-spinner')) {
            $(this).addClass("loading");
        }
    });
});
