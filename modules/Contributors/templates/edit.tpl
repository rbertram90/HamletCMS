<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <h2>Change group</h2>
            <form class="ui form" method="POST">
                <select class="ui dropdown" name="fld_group">
                {foreach $groups as $group}
                    <option value="{$group->id}">{$group->name}</option>
                {/foreach}
                </select>

                <button class="ui teal button">Update</button>
                <button type="button" class="ui button" onclick="window.history.back();">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
$(".ui.dropdown").dropdown();
</script>