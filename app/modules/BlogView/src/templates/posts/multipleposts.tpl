<div class="ui divided very relaxed link items">
    {$posts}
</div>

{$numPages = ceil($totalnumposts / $postsperpage)}
{$paginator->showPagination($numPages, $currentPage)}