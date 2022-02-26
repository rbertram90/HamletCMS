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

Data is stored both in MySQL database and within the file system
 * default.php - one line file which includes the blog setup script
 * default.css - stylesheet from templates
 * images
 * blog_config.json - any other configuration changes made through settings menu
 * blog specific smarty templates

### Installation
1. `git clone https://github.com/rbertram90/HamletCMS-project.git my-project`
2. Remove the .git folder and run `git init` to start your project repository
3. `composer install`
4. Create database (default name = hamlet)
5. Copy `/config/config_default.json` -> `/config/config.json`
6. Change database connection details in config.json
7. Change `environment.root_directory` and `environment.public_directory` in config.json to reflect your file system
8. Run `php ./app/updatepublic.php` to copy accross required Hamlet files into your public directory - this will overwrite both `index.php` and `.htaccess` files if they already exist
9. Navigate to the site - you should be redirected to /cms/install
10. Complete the install form
11. Check everything is working, if not raise a <a href="https://github.com/rbertram90/HamletCMS/issues">Github issue</a> with details!
