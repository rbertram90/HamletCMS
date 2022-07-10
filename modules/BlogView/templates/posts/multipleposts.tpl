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
        <div class="ui {$postConfig.parentclasslist} cards">{$posts}</div>
    {elseif $postConfig.listtype == 'none'}
        {$posts}
    {else}
        <div class="ui {$postConfig.parentclasslist} items">{$posts}</div>
    {/if}
{else}
    <div class="ui {$postConfig.parentclasslist} items">{$posts}</div>
{/if}

<div class="ui hidden divider"></div>

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
{elseif strlen($posts) > 0}
    <div class="ui pagination menu">
        {if $currentPage > 1}
            <a href="?s={$currentPage - 1}" class="item"><i class="left angle icon"></i></a>
        {/if}

        {if $pagecount > 10}
            {$first = $currentPage - 5}
            {if $first < 1}
                {$first = 1}
            {/if}

            {$last = $first + 10}
            {if $last > $pagecount}
                {$last = $pagecount}
            {/if}
        {else}
            {$first = 1}
            {$last = $pagecount}
        {/if}

        {for $i=$first to $last}
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

<script>
    $(document).ready(function() {
        $("a[data-modal]").click(function (e) {
            e.preventDefault();
            var modalClass = $(this).data('modal');
            $('.' + modalClass).modal('show');
        });
    });
</script>