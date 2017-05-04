<?php
    use \Michelf\Markdown;

    require_once SERVER_ROOT.'/app/view/view_post_summary.php';
?>

<div class="search-container">

    <h1>Search</h1>

    <?php if($search_string !== null): ?>

        <h2>You Searched For: &quot;<?= $search_string ?>&quot;</h2>

        <h3>Found <?= count($search_results) ?> Result(s)</h3>

        <?php foreach($search_results as $matching_post): ?>
        <div class="search-result">
            <h3><a href="<?= CLIENT_ROOT_BLOGCMS ?>/blogs/<?= $this->blogID ?>/posts/<?= $matching_post['link'] ?>"><?= $matching_post['title'] ?></a></h3>
            <?php $trimmedContent = Markdown::defaultTransform($matching_post['content']); ?>
            <p><?= substr(strip_tags($trimmedContent), 0, 300) ?></p>
         </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <form action="<?= CLIENT_ROOT_BLOGCMS ?>/blogs/<?= $this->blogID ?>/search" method="GET">
        <input type='text' value='<?= $search_string ?>' placeholder="Search Blog" name='q' /><input type='submit' name='go' value='Search' />
    </form>

</div>