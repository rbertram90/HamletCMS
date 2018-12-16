<div class="ui items">
    {$posts}
</div>

{$numPages = ceil($totalnumposts / $postsperpage)}
{$paginator->showPagination($numPages, $currentPage)}