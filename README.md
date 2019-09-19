# HamletCMS

## About
This is a blogger style content management system for writing articles to blogs. A hobby project, it is not in use in the real world as yet.

### Current status
In development: dev-master has known issues

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
Front end utilises Semantic UI (https://semantic-ui.com/)

Back end is all vanilla PHP other than views which are now largely all using the Smarty templating engine. The code is heavily dependent on the rbwebdesigns\core library.

Data is stored both in MySQL database and within the file system (defaulted to /public/blogdata)
 * default.php - one line file which includes the blog setup script
 * default.css - stylesheet from templates
 * images folder
 * blog_config.json - any other configuration changes made through settings menu

### Installation
1. Clone repository
2. Create database (default name = blog_cms)
3. Copy app/config/config_default.json -> app/config/config.json
4. Change database connection details in config.json
5. Change root_directory in config.json
6. Run `composer update` from /app directory
7. Navigate to the site - you should be redirected to /cms/install.php
8. Complete the install form
9. Check everything is working - if not please raise a ticket with details!

### Dependencies
 * codeliner/array-reader" : "~1.0",
 * smarty/smarty": "~3.1",
 * michelf/php-markdown": "1.4.1"
