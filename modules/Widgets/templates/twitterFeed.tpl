{if $type == 'list'}
    <a class="twitter-timeline" data-tweet-limit="{$limit}" href="https://twitter.com/TwitterDev/lists/{$list}?ref_src=twsrc%5Etfw">{$heading}</a>
{else}
    {* timeline *}
    <a class="twitter-timeline" data-tweet-limit="{$limit}" href="https://twitter.com/{$handle}?ref_src=twsrc%5Etfw">{$heading}</a>
{/if}

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
