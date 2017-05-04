<?php
/**
* @backupGlobals disabled
* @backupStaticAttributes disabled
*/

require_once '../../../root.inc.php';
require_once SERVER_ROOT.'/vendor/autoload.php';
require_once SERVER_PATH_CORE.'/core.php';
require_once SERVER_ROOT.'/envsetup.inc.php';

class blogtest extends PHPUnit_Framework_TestCase {

    private $blogcms_db;
    private $modelBlog;

    public function __construct() {
        $this->blogcms_db = $GLOBALS['cms_db'];
        $this->modelBlog = new ClsBlog($this->blogcms_db);
    }

    public function testCountBlogsByLetter() {

        // Get the array from the database
        $arrayCounts = $this->modelBlog->countBlogsByLetter();

        // Simply check we are getting an array with the expect number of elements
        $this->assertEquals(27, count($arrayCounts));
    }

}
    
?>