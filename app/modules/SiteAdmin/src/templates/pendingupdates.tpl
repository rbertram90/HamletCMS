<h1 class="ui heading">Database updates</h1>

<p>{count($modules)} update(s) pending</p>

<table class="ui celled table">
<thead>
    <tr>
        <th>Module name</th>
        <th>Current version</th>
        <th>Latest version</th>
    </tr>
</thead>
<tbody>
{foreach from=$modules item=module}
    <tr>
        <td>{$module.name}</td>
        <td>{$module.current}</td>
        <td>{$module.latest}</td>
    </tr>
{/foreach}
</tbody>
</table>

<form>
    <button class="ui button teal">Run updates</button>
    <a href="/cms/admin/modules" class="ui button">Back</a>
</form>