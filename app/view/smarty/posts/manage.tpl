{* Manage Posts *}

<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog['id']}", "{$blog.name}"), 'Manage Posts')}
        </div>
    </div>
    
    <div class="two column row">
        <div class="column">
            {viewPageHeader('Manage Posts', 'papers.png', "{$blog.name}")}
        </div>
        <div class="column">
            <div class="ui form">
                <div class="ui horizontal segments margin" style="margin-top:0px; margin-bottom:0px;">
                    <div class="ui segment">
                        <label for="numtoshow">Show</label>
                        <select id="numtoshow" name="numtoshow">
                            <option>5</option>
                            <option selected>10</option>
                            <option>15</option>
                            <option>20</option>
                        </select>
                    </div>
                    <div class="ui segment">
                        <label for="sortby">Sort</label>
                        <select id="sortby" name="sortby">
                            <option value="timestamp DESC">Date Posted (Newest First)</option>
                            <option value="timestamp ASC">Date Posted (Oldest First)</option>
                            <option value="title ASC">Title (A First)</option>
                            <option value="title DESC">Title (Z First)</option>
                            <option value="author_id ASC">Author ID (Low -> High)</option>
                            <option value="author_id DESC">Author ID (High -> Low)</option>
                            <option value="hits DESC">Views (Most First)</option>
                            <option value="hits ASC">Views (Least First)</option>
                            <option value="uniqueviews DESC">Visitors (Most First)</option>
                            <option value="uniqueviews ASC">Visitors (Least First)</option>
                            <option value="numcomments DESC">Comments (Most First)</option>
                            <option value="numcomments ASC">Comments (Least First)</option>
                        </select>
                    </div>
                    <div class="ui segment">
                    <div class="inline field">
                        <div class="ui checkbox">
                            <input type="checkbox" class="hidden" id="filterdrafts" name="filterdrafts" checked>
                            <label>Show Drafts</label>
                        </div>
                    </div>
                    <div class="inline field">
                        <div class="ui checkbox">
                            <input type="checkbox" class="hidden" id="filterscheduled" name="filterscheduled" checked />
                            <label>Show Scheduled</label>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <script>$('.ui.checkbox').checkbox();</script>
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            <div id="posts_display">Loading...</div>
        </div>
    </div>
    
</div>
    
<script>
    var refreshData = function(pagenum) {
        var numtoshow = $("#numtoshow").val();
        var sortby = $("#sortby").val();
        var filterdrafts = $("#filterdrafts").is(':checked');
        var filterscheduled = $("#filterscheduled").is(':checked');

        $.get("/ajax/get_posts",
            {
                b:{$blog.id},
                s:pagenum,
                n:numtoshow,
                o:sortby,
                fd:filterdrafts,
                fs:filterscheduled

            }, function(data) {
                $("#posts_display").html(data);
            }
        );
    };
    $("#numtoshow").change(function() {
        refreshData(1); // change number that is shown - return to first page
    });
    $("#sortby").change(function() {
        refreshData(1); // change sort & return to first page
    });
    $("#filterdrafts").change(function() {
        refreshData(1);
    });
    $("#filterscheduled").change(function() {
        refreshData(1);
    });

    // Init
    refreshData(1);

</script>