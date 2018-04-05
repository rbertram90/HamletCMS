<h1>Homepage</h1>

<div class="ui segment">
    <h2>What is this page?</h2>
    <p>This is the default front-page for a website using <a href="http://github.com/rbertram90/blog_cms" target="_blank">Blog CMS</a> as a backend content management system. These pages will likely include links to the blogs that have been created within the CMS. There will be a range of example views made available to place on these pages <em>(coming soon!)</em>.</p>

    <p>To change the contents of this file edit <strong>/app/view/smarty/public/home.tpl</strong></p>
</div>

<div class="ui segment">
    <h2>Adding further pages</h2>
    <ol>
        <li>Add a public function to /app/controller/public_controller.inc.php
        <li>Name the function the same as the url path - so for an about page (example.com/about) the function should be called 'about'
        <li>Call $this->response->write('&lt;template-path&gt;'); to output a smarty file (<a href="https://github.com/rbertram90/core/wiki/response" target="_blank">see full response documentation</a>)
        <li>Create the smarty template file under <strong>/app/view/smarty/public</strong>
        <li>Note: by default <a href="https://semantic-ui.com/">semantic UI</a> is included
    </ol>
</div>

<a href="/cms" class="ui teal button">Login to CMS</a>