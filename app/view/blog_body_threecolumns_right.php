<div class="leftcolumn">
    <div class="actions">
        <?php include SERVER_ROOT.'/app/view/blog_actions.php'; ?>
    </div>
    <?php if(array_key_exists('leftpanel', $DATA['widget_content'])) echo $DATA['widget_content']['leftpanel']; ?>

</div><div class="rightcolumn">
    <?php if(array_key_exists('rightpanel', $DATA['widget_content'])) echo $DATA['widget_content']['rightpanel']; ?>

</div><div class="posts">
    <div id="messages">
    <?php
    if(isset($_SESSION['messagetoshow']) && $_SESSION['messagetoshow'] != false) {
        echo $_SESSION['messagetoshow'];
        $_SESSION['messagetoshow'] = false;
    }
    ?>
    </div>
    <?=$DATA['page_content']?>
</div>