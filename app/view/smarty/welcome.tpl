<img src="/images/bluesky.jpg" style="width:100%" />

<h1>Welcome to BLOG CMS</h1>

<h2>Recent Changes</h2>

<h3>Version 1.8.0</h3>
<p>New Feature: Manage image files for blog</p>
<ul>
<li>Added &#39;manage images&#39; screen which gives details of all images uploaded to blog</li>
<li>Added image upload feature using dropzone.js</li>
<li>Added ability to delete uploaded images</li>
</ul>
<p>See post: <a href="http://www.opencoding.co.uk/posts/quick-file-uploads-with-dropzonejs">opencoding.co.uk/posts/quick-file-uploads-with-dropzonejs</a> for my comments on implementing dropzone.js</p>


<h3>Version 1.7.2</h1>
<p>Updates aren't so much new features this time - mainly bug fixes!</p>
<ul>
    <li>When image is added to post the markdown code is added at current cursor position</li>
    <li>Contributors screen - last posted includes future posts</li>
    <li>Unable to remove footer image</li>
    <li>Click on a tag with a space in it and it redirects to homepage</li>
    <li>Next Post Button seems to link to the most recent post all the time - also if that is a scheduled post then it doesn't ignore if you are logged out.</li>
    <li>Post search on blog doesn't run output through markdown - needs also to strip any HTML tags</li>
</ul>
<ul>
    <li>Option to hide tags on post footer</li>
    <li>Improved post date/time formatting</li>
    <li>Changes icons on settings screens to match main menu</li>
    <li>Search posts now also searches tags as well as the post titles</li>
</ul>

<h3>Version 1.7</h3>

<p>Nothing has changed for the last 7 months hence a big update this time, not necessarily in terms of changes that a user would notice but the majority of the CMS now uses the Smarty PHP templating system. I started the switch over about a month and a half ago, due to lack of free time there were large gaps where I didn't look at this project however over the last week or so I wanted to make a push to get it finished.</p>

<p>After reviewing the code structure there were too many blurred lines between the controller and the view, I could have simply created a view handler class and left the views themselves somewhat untouched but the addition of a templating engine has made the code substancially more readable and forced me to review the code written.</p>

<ul>
<li>Smarty Templates</li>
<li>Logo colour change</li>
<li>Updated UI for manage contributors page</li>
</ul>

<h3>Version 1.6</h3>
<p>Here are the changes for version 1.6</p>

<ul>
<li>Manage Posts - Interface is completely changed - data is now loaded via ajax so can now filter and sort the results</li>
<li>Blog Overview - Now has statistics panels with number of comments, number of contributors, number of post views and number of posts</li>
<li>UI - Font has changed to open sans, refined the colour scheme</li>
<li>Add/ Edit Post - Have created a second column for a better screen layout on desktop</li>
<li>Pages - A feature I have been meaning to add for ages - the ability to mark posts as 'pages' which appear in the top navigation of the blog</li>
<li>Search Widget - Free text search from a blog on the content and post title</li>
<li>Changes page has been changed to a welcome page (still in development)</li>
<li>Blogs page has fewer details listed for a cleaner look</li>
<li>Autosaving on post create and edit - this was a fairly substantial change, involved re-writing a lot of the add post code to automatically create a draft so that if the user was to navigate accidentally away from the post then they could recover from where they left off. If the post is already created then an 'autosave' is added to a seperate table and presented to the user when they go back in.</li>
</ul>


<h3>Version 1.5</h3>
<p>Once again this is a distant recall of the changes - these were actually changed in September last year!</p>
<ul>
    <li>Added different types of post - can now have a video post which has a youtube/ vimeo video at the top, will also be a gallery post however this was never finished - and still isn't!</li>
    <li>Added 'edit header' to the settings section which allows owner to add background image to blog header</li>
    <li>Added generic page header function to reduce repeated code</li>
    <li>Section to review comments made on the blog</li>
</ul>


<h3>Version 1.4</h3>
<ul>
<li>Contributors Re-enabled!
<ul>
<li>Can Add (but not edit or delete) contributors to a blog</li>
<li>Can set the level of permission they have - can either add and edit their own posts or change everything!</li>
<li>Added a view contributors page which shows when they last posted and how many posts they have made</li>
</ul></li>
<li>code improvements behind the scenes</li>
<li>Data on posts - when a post is viewed directly then the user's IP is recorded so that unique hits and total views are known by the authors of the blog.</li>
<li>drag and drop control to change the order and turn on or off widgets - this is not perfect as you can't specifically choose the position of the widget within the group but is better than what was there!</li>
<li>added ability to use markdown (PHP Markdown) in posts rather than my 'wiki' markup as it's less clunky however...
<ul>
<li>removed rtf style editor as it didn't work with markdown. The idea was to use markdown combined with the opposite plugin 'HTML-to-markdown' to switch between HTML and markdown. Unfortunately when using these in combination with the 'content-editable' attribute it was not producing consistant results - so it's removed for now!</li>
</ul></li>
<li>little style tweaks - notably the buttons use a bootstrap 2 style</li>
</ul>


<h3>Version 1.3</h3>
<ul>
<li>Change footer content
<ul>
<li>form to allow text to be changed in footer</li>
<li>one or two columns</li>
<li>background image to show</li>
<li>background image alignment options</li>
</ul></li>
<li>All the usual tweaks and bug fixes</li>
<li>The rest was probabily behind the scenes code tidying...</li>
</ul>


<h3>Version 1.2</h3>
<ul>
<li>Add ability to control ordering and visibility of widgets on the side menu.</li>
<li>Add new widget for recent comments.</li>
<li>New Design Template - now live on this blog.</li>
<li><strong>Images</strong> in posts!</li>
<li>Bug Fixes</li>
<li>Add explore section to explore most followed blogs.</li>
<li>Improvement to allow font sizes to be specified within blog designer.</li>
<li>Changes to make the code more portable between live and local copies.</li>
<li>Prevented tags from being cut off half way through in blog post summaries.</li>
</ul>


<h3>Version 1.1</h3>
<ul>
<li>Wiki Language can now be used in blog posts and a (really) basic rtf editor is used as default so headings, bold, italic and underlined text can be applied without any knowledge of markup.</li>
<li>Users can now favorite blogs. Posts from favorite blogs within the last 7 days will appear on the dashboard.</li>
<li>Post Settings - Comments can be enabled/ disabled on a post by post basis.</li>
<li>Post Settings - Number of posts to show per page on blog can be specified.</li>
<li>Blog Settings - Blog Visibility can be changed to only allow logged in users/ friends/ nobody or everyone to read your blog.</li>
<li>Various bug fixes.</li>
</ul>

<h3>November 2013</h3>
<ul>
    <li>Version 1.1 Planned and now testing with following additional features</li>
    <ul>
        <li>Ability to favorite a blog.</li>
        <li>Recent posts from your favorite blogs shown on the homepage.</li>
        <li>Ability to add and edit blog posts in wiki language or using an rtf style editor.</li>
        <li>Several little bug fixes.</li>
    </ul>
    <li>Site has been put live - beginning of november?</li>
</ul>


<h3>Version 1.0 Feature List</h3>
<ul>
<li>Create a blog</li>
<li>Edit Stylesheet</li>
<li>Edit Template with a couple of standard templates</li>
<li>Edit (Some) Colours and Fonts with the Form</li>
<li>Choose the date and time format on blog posts</li>
<li>Create New Posts (Title, Content, Tags)</li>
<li>Create New Posts as Drafts</li>
<li>Edit Existing Posts (Title, Content, Tags)</li>
<li>Delete Posts</li>
<li>Allow logged in users only to add comments to your posts</li>
<li>Preview post in a new window (no stylesheet applied)</li>
</ul>


<h3>October 2013</h3>
<ul>
	<li>Have hidden many 'features' due to incompleteness, aiming to put 1.0 up on live website towards the end of the month. Full feature list will be published.</li>
</ul>
<h3>September 2013</h3>
<ul>
	<li>Completed the initial blog colour scheme creator, really basic at the moment (further desc below)</li>
	<li>The system can now use a JSON file to read in css overides, the plan is that this file will be able to be uploaded by anyone who knows what they are doing (template designers) then the people who don't know can use the point and click menus to change the colours, font-style and general style of their blog.</li>
	<li>A post tag system is in place, added at some point in the last month</li>
	<li>Improved navigation around back-end, extra menu items are now on the left menu when you have a blog selected</li>
	<li>General UI improvements, gradients on menu links and dropdown lists to select action to take, crumbtrail menu with glossy background</li>
	<li>Manually Edit Stylesheet</li>
	<li>Fixed Delete Post function</li>
	<li>Added Pagination to the blog</li>
	<li>Created a blog overview page, so that the links to the blog in the back-end point to the dashboard rather than keep sending you to view the blog.</li>
</ul>
<h3>August 2013</h3>
<h3>17/08/13</h3>
<ul>
	<li>Restored the link to the template chooser</li>
	<li>Added Template to switch the menu from the left to right of the blog
</ul>
<h3>16/08/13</h3>
<ul>
	<li>Early Beginnings of blog colour scheme creator
</ul>
<h3>14/08/13</h3>
<ul>
	<li>Comments on blog posts have mostly been restored!
	<li>Still need to implement option to allow anon users to post comments!
</ul>
<h3>12/08/13</h3>
<ul>
	<li>Blog Summary Screen Replaces view blog for CMS view navigation
</ul>
<h3>01/08/13</h3>
<ul>
	<li>Search Blogs by name (a-z) menu implemented in explore page</li>
</ul>

<h3>July 2013</h3>
<h3>20/07/13</h3>
<ul>
	<li>Formatting of the date and time on posts is now possible!
	<li>Changing the name and description is now fixed!
	<li>'Save as Draft' for posts!
</ul>
<h3>18/07/13</h3>
<ul>
	<li>Alpha - You can now add contributors to the blog!
</ul>

<h2>Planned Improvements</h2>
<ul>
	<li>Comment Approval Option
	<li>Search Blogs on site - needs more detail (v2?)
	<ul>
		<li>Blog Tags / Categories
		<li>Hide blog from search option
	</ul>
	<li><strike>Post Tags</strike>
	<li>Fix Comments
	<li>Work on code security, make sure users have access to run scripts
	<li>Bulletproof code, prepare to host on rbwebdesigns.co.uk
	<ul>
		<li>Want to use this system to blog on opencoding.co.uk, any development blogs, any other blogs i can think of.
	</ul>
</ul>