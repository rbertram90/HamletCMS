<?php
require_once SERVER_ROOT.'/app/view/widgets/widget_interface.php';
require_once SERVER_ROOT.'/app/view/widgets/widgets_parentclass.php';

function generateWidgets($arrayWidgetConfig, $pobjPosts, $pobjBlogs, $pobjComments, $pobjBlog, $pobjUsers=null) {

    // Ensure the widgets are ordered correctly
    // orderList($arrayWidgetConfig);
  $return = array();
  
  // No widgets
  if(strtolower(getType($arrayWidgetConfig)) != 'array') return $return;

  foreach($arrayWidgetConfig as $category => $widgets) {
    
    $lsWidgetHTML = "";
    
    // Print out widgets
    foreach($widgets as $widget):

        $key = $widget['type'];
        
        $lsWidgetHTML.= '<div class="widget" id="widget_'.$key.'">';
        
        $className = ucfirst(strtolower($key)); // make the first character uppercase - the rest lower
        $fileName = SERVER_ROOT.'/app/view/widgets/'.$key.'.php';
        
        if(file_exists($fileName)) {
            include_once $fileName;
            $widgetInstance = new $className($widget, $pobjBlog, $pobjBlogs, $pobjPosts, $pobjComments, null, $pobjUsers);
            $lsWidgetHTML.= $widgetInstance->generate();
        }
        else {
            if(IS_DEVELOPMENT) $lsWidgetHTML.= showError("Unable to find widget class file - ".$key);
        }
        
        $lsWidgetHTML.= '</div>';
    
        // endif;
    endforeach;
    
    $return[$category] = '<div class="widgets">'.$lsWidgetHTML.'</div>';
  }
  
  return $return;
}

function orderList(&$arrayWidgetConfig) {
    $array_keys = array_keys($arrayWidgetConfig);
    if(array_key_exists('order', $arrayWidgetConfig[$array_keys[0]])) {
        sksort($arrayWidgetConfig, 'order', true);
    }
}
?>