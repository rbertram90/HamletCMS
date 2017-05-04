<?php
class BlogCMSWidget implements BlogCMSWidgetInterface
{
	public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUser)
	{
		
	}
	
    protected function setupConfig() {
        
        // Apply Defaults If needed
        if(getType($this->config) == 'array')
		{
            foreach($this->defaults as $key => $value)
			{
                if(!array_key_exists($key, $this->config)) $this->config[$key] = $value;
            }
        }
        else
		{
            $this->config = $this->defaults;
        }
    }
	
	public function generate()
	{
		
	}
}
?>