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

Back-end is all vanilla PHP other than views which are now largely all using the Smarty templating engine. The code is dependent on the `rbwebdesigns\core` library, see https://github.com/rbertram90/core.

Data is stored both in MySQL database and within the file system (defaulted to /public/blogdata)
 * default.php - one line file which includes the blog setup script
 * default.css - stylesheet from templates
 * images
 * blog_config.json - any other configuration changes made through settings menu
 * blog specific smarty templates

### Installation
1. Clone repository
2. Create database (default name = hamlet)
3. Copy app/config/config_default.json -> app/config/config.json
4. Change database connection details in config.json
5. Change root_directory in config.json
6. Run `composer update` from /app directory
7. Navigate to the site - you should be redirected to /cms/install.php
8. Complete the install form
9. Check everything is working - if not please raise a ticket with details!