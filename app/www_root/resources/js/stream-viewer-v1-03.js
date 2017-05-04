/****************************************************************************************
    stream viewer gallery script - version 1.02
    
    @description this was a simple image gallery viewer script that was originally
    written for digipix project which is a photo sharing site written in php. This version
    was modified for RBwebdesigns main site.
    
    Improvements from v1.1
     * The original could only change 3 photos at a time this has a single variable
       in which an ODD NUMBER ONLY can be put to choose the number of thumbs that should
       be shown at one time.
    
    @author R Bertram
    
    @date-created 20/01/2013
        
    HTML Requirements
     * two buttons with id streamviewer_next and streamviewer_prev
     
     * the following code:
     
    $(document).ready( function() {
        var galleryViewer = new StreamViewer(x);
        galleryViewer.initialize();
    });
    
     * Where x is the number of thumbnails to be shown at a time
    
     * Image thumbnails with the class 'stream-thumb' - all of the images with this class
       will be displayed in the viewer.
    
     * One of the images MUST have the attribute 'data-active="true"'

****************************************************************************************/

// stream-viewer v1.3 (c) RBwebdesigns.co.uk 2013
// Note this must be intialized after the DOM has loaded!
function StreamViewer(pX) {

    // Get the index of the current item
    var p = $(".stream-thumb").index($(".stream-thumb[data-active='true']"));
    // Total number of thumbnails
    var n = $(".stream-thumb").length;
    //The number that should be shown -> note this will only work with odd numbers!
    var x = pX;
    var y = (x - 1) / 2;
    // Holder for the current middle image
    var m = p;

    this.initialize = function() {
        // Get the current photo
        $(".stream-thumb[data-active='true']").addClass("stream-active-thumb");
        
        if(p < (x-1)) {
            // In the first x items -> Hide all images with index greater than x-1
            $(".stream-thumb:gt(" + (x - 1) + ")").hide();
        } else if( p > (n-1) ) {
            // In the last x items
            $(".stream-thumb:lt(" + (n-x) + ")").hide();
        } else {
            // Some where in the middle -> show equal amount either side
            $(".stream-thumb:lt(" + (p-y) + ")").hide();
            $(".stream-thumb:gt(" + (p+y) + ")").hide();
        }
    };

    $("#streamviewer_next").click( function() {
        if(n-1 > m + x + y) {
            // Ok, still at least x more to show
            var y1 = (x + 1) / 2;
            var y2 = y1 + (x);
            // Show the correct 3
            $(".stream-thumb").hide();
            $(".stream-thumb").slice(m+y1,m+y2).show();
            // set the new middle
            m = m + x;
        }
        else {
            // Will reach end on this click -> just show last 3
            $(".stream-thumb:lt(" + (n-x) + ")").hide();
            $(".stream-thumb:gt(" + (n-x-1) + ")").show();
            m = n - ((x + 1) / 2);
        }
    });
    
    $("#streamviewer_prev").click( function() {
        if(m > x + y) {
            // Ok, still at least 3 more to show
            // Hide all
            $(".stream-thumb").hide();
            // Show the correct 3
            var y1 = x + ((x - 1) / 2);
            var y2 = ((x - 1) / 2 );
            $(".stream-thumb").slice(m-y1,m-y2).show();
            // set the new middle
            m = m - x;
        }
        else {
            // Will reach end on this click -> just show first 3
            $(".stream-thumb:gt(" + (x-1) + ")").hide();
            $(".stream-thumb:lt(" + x + ")").show();
            m = y;
        }
    });
}

/*$(document).ready( function() {
    var galleryViewer = new StreamViewer(3);
    galleryViewer.initialize();
});*/