# Blog CMS

## About
This is a blogger style content management system for writing articles to blogs

### Current functionality
 * Create multiple blogs
 * Create, edit and delete posts
 * Edit Stylesheet
 * Apply templates
 * Add / Remove Contributors
 * Change post formatting
 * Change header and footer content
 * Upload images
 * Add tags to posts
 
## Technical
Front end utilises Semantic UI (https://semantic-ui.com/) to give it a modern look and feel (this update is still ongoing!)

Back end is all vanilla PHP other than the views which are now mainly all using the Smarty templating engine

The core part of the system was originally seperate under rbwebdesigns but has now been included as part of
blog cms - still uses original namespace (need to change?)

Blog data is stored both in MySQL database and within files defaulted to /app/public/blogdata
 * default.php - one line file which includes the blog setup script
 * default.css - stylesheet from templates
 * images folder
 * blog_config.json - any other configuration changes made through settings menu


### Dependencies

 * codeliner/array-reader" : "~1.0",
 * smarty/smarty": "~3.1",
 * michelf/php-markdown": "1.4.1"
 