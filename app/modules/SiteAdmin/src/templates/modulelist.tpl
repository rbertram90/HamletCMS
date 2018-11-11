<h1 class="ui heading">Modules</h1>

<table class="ui celled table">
<thead>
    <tr>
        <th>Name</th>
        <th>Enabled?</th>
        <th></th>
    </tr>
</thead>
<tbody>
{foreach from=$modules item=module}
    <tr>
        <td>{$module.name}</td>
        <td>{$module.enabled}</td>
        <td>
            {if $module.enabled and not $module.locked}
                <a href="/cms/admin/uninstallmodule/{$module.name}">Uninstall</a>
            {elseif not $module.locked}
                <a href="/cms/admin/installmodule/{$module.name}">Install</a>
            {else}
                <em>Locked</em>
            {/if}
        </td>
    </tr>
{/foreach}
</tbody>
</table>

<h2>Admin actions</h2>

<a href="/cms/admin/modulescan" class="ui labeled icon button"><i class="sync icon"></i>Scan for new modules</a>
<a href="/cms/admin/reloadcache" class="ui labeled icon button"><i class="sync icon"></i>Reload system caches</a>
<a href="/cms/admin/updatedatabase" class="ui button">Check for database updates</a>