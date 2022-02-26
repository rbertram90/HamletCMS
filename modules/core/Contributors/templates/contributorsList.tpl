<h3>{$heading}</h3>

<div class="ui {$columns} cards">
    {foreach $contributors as $contributor}
        <div class="ui card">
            <div class="image">
                <img src="{$contributor->avatar()}">
            </div>
            <div class="content">
                <a class="header" href="/cms/account/user/{$contributor->id}">{$contributor->name} {$contributor->surname}</a>
                <div class="meta">{$contributor->username}</div>
                <div class="description">{$contributor->description}</div>
            </div>
            <div class="extra content">
                <span class="right floated"><i class="venus mars icon"></i> {$contributor->gender}</span>
                {if $contributor->location}
                    <span><i class="map marker alternate icon"></i> {$contributor->location}</span>
                {/if}
                
            </div>
        </div>
    {/foreach}
</div>