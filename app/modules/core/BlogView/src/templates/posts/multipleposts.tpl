{*
 * Multiple posts template
 *
 * Handles pagination and post wrappers
 *
 * Variables:
 *  - $posts
 *  - $postsperpage
 *  - $currentPage
 *  - $totalnumposts
 *  - $paginator
 *  - $blog
 *  - $userIsContributor
 *}
 
{if isset($postConfig)}
    {if $postConfig.listtype == 'cards'}
        <div class="ui cards">{$posts}</div>
    {elseif $postConfig.listtype == 'none'}
        {$posts}
    {else}
        <div class="ui items">{$posts}</div>
    {/if}
{else}
    <div class="ui items">{$posts}</div>
{/if}

{if $loadtype == 'loadmore'}
    <button class="loadmoreposts ui button">Load more</button>
    <script>
        var currentPage = 1;
        $(".loadmoreposts").click(function(e) {
            currentPage++;
            $(this).prop('disabled', true).addClass('loading');
            $.get('{$blog->relativePath()}/posts/loadmore?page=' + currentPage, function(data) {
                if ($('.column.posts .ui').length > 0) {
                    $elemt = $('.column.posts > div')
                }
                else {
                    $elemt = $('.column.posts');
                }
                $elemt.append(data);
                $(".loadmoreposts").prop('disabled', false).removeClass('loading');
            });
        });
    </script>
{else}
    <div class="ui pagination menu">
        {if $currentPage > 1}
            <a href="?s={$currentPage - 1}" class="item"><i class="left angle icon"></i></a>
        {/if}

        {for $i=1 to $pagecount}
            {if $i == $currentPage}
                <a class="active item">{$i}</a>
            {else}
                <a href="?s={$i}" class="item">{$i}</a>
            {/if}
            
        {/for}

        {if $currentPage < $pagecount}
            <a href="?s={$currentPage + 1}" class="item"><i class="right angle icon"></i></a>
        {/if}
    </div>
{/if}