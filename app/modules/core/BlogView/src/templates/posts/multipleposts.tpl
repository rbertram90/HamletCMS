{*
Variables:
    $posts
    $postsperpage
    $currentPage
    $totalnumposts
    $paginator
    $blog
    $userIsContributor
*}
{if isset($postConfig)}
    {if $postConfig.listtype == 'cards'}
        <div class="ui cards">{$posts}</div>
    {elseif $postConfig.listtype == 'none'}
        <div class="posts">{$posts}</div>
    {else}
        <div class="ui items">{$posts}</div>
    {/if}
{else}
    <div class="ui items">{$posts}</div>
{/if}

{$numPages = ceil($totalnumposts / $postsperpage)}
{$paginator->showPagination($numPages, $currentPage)}