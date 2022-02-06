# HamletCMS

## About
This is a blogger style content management system for writing articles to blogs. A hobby project, it is not in use in the real world as yet.

Check out the wiki for some documentation https://github.com/rbertram90/HamletCMS/wiki.

### Current status
In development: dev-master has known issues

### Current functionality
 * Create multiple blogs
 * Create, edit and delete posts
 * Edit stylesheet
 * Import external stylesheets
 * Apply templates
 * Add multiple contributors
 * Header, footer and post content are fully customisable
 * Add tags to posts

## Technical
Front end utilises Semantic UI (https://semantic-ui.com/)

The application is build upon my own library of PHP classes, see https://github.com/rbertram90/core. Views use the Smarty templating engine.

Data is stored both in MySQL database and within the file system (defaulted to /public/blogdata)
 * default.php - one line file which includes the blog setup script
 * default.css - stylesheet from templates
 * images
 * blog_config.json - any other configuration changes made through settings menu
 * blog specific smarty templates

### Installation
1. Clone repository
2. Run `composer install`
3. Create database (default name = hamlet)
4. Copy `/config/config_default.json` -> `/config/config.json`
5. Change database connection details in config.json
6. Change `environment.root_directory` and `environment.public_directory` in config.json to reflect your file system
7. Run `php ./app/updatepublic.php` to copy accross required Hamlet files into your public directory - this will overwrite both `index.php` and `.htaccess` files if they already exist
8. Navigate to the site - you should be redirected to /cms/install
9. Complete the install form
10. Check everything is working, if not raise a <a href="https://github.com/rbertram90/HamletCMS/issues">Github issue</a> with details!
