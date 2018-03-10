<div class="posts">
    <div id="messages">
    <?php
    if(isset($_SESSION['messagetoshow']) && $_SESSION['messagetoshow'] != false) {
        echo $_SESSION['messagetoshow'];
        $_SESSION['messagetoshow'] = false;
    }
    ?>
    </div>
    <?=$DATA['page_content']?>
    <div class="actions">
        <?php include SERVER_ROOT.'/app/view/blog_actions.php'; ?>
    </div>
</div>