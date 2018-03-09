var addFavourite = function (pBlogid) {
	if(confirm("Add blog to favourites list?")) {
		$.get(folder_root + "/ajax/ajax_addFavourite.php?blogid=" + pBlogid, function(data) {
			$("#btn_favourite").addClass("btn_green");
			$("#btn_favourite").html("Favourite Added Successfully!");
			$("#btn_favourite").attr("onclick","removeFavourite('" + pBlogid + "'); return false;");
		});
	}
};
var removeFavourite = function (pBlogid) {
	if(confirm("Remove blog to favourites list?")) {
		$.get(folder_root + "/ajax/ajax_removeFavourite.php?blogid=" + pBlogid, function(data) {
			$("#btn_favourite").removeClass("btn_green");
			$("#btn_favourite").html("Favourite Removed Successfully!");
			$("#btn_favourite").attr("onclick","addFavourite('" + pBlogid + "'); return false;");
		});
	}
};