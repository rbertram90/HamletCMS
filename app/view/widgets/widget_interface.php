<?php
interface BlogCMSWidgetInterface {
    
    // All widgets will have all models as parameters as we don't know which widgets require which models
    public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUsers);
    
    // Form for which the widget can be customised - standard field should be name
    // public function optionsForm();
    
    // Method to show the widget on the blog
    public function generate();
    
}
?>