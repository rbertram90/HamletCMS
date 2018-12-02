<div class="ui very relaxed link items">
    {$posts}
</div>

{$numPages = ceil($totalnumposts / $postsperpage)}
{$paginator->showPagination($numPages, $currentPage)}