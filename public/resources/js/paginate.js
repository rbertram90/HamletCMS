// Global Defaults
var currentpagenum = 1;
var currentlimit = 5;
var currentsort = 0;
var currentdir = 'asc';

function loadPage(pagenum, limit, sortcolumn, sortdir) {
    var currentpagenum = pagenum;
    if (arguments.length == 2) {
        // no sort
        sortcolumn = currentsort;
        sortdir = currentdir;
    } else {
        // sort specified - save to globals
        currentsort = sortcolumn;
        currentdir = sortdir;
    }
    $("#tableusers").load('getusers.php', {
        page:pagenum,
        limit:limit,
        sortby:sortcolumn,
        sortdir:sortdir
    });
}

function sortdata(column, direction) {
    loadPage(currentpagenum, currentlimit, column, direction);
}