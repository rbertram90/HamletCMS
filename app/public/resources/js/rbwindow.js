/**
--------------------- IMPORTANT ---------------------
    This script requires /resoures/js/ajax.js
    This script requires jQuery
-----------------------------------------------------

    Usage:
    
    // Create Window Object
    var win = new rbwindow(options);
    
    // Show Window
    win.show();
    
    // Hide Window
    win.remove();
    
**/
function rbwindow(config) {

	// Configuration JSON (optional)
	this.windowConfig = config;
	
	// Default Params
	this.windowHeight = 400;
	this.windowWidth = 600;
	this.isModal = false;
	this.title = "Pop Up Window";
	this.url = "";
	this.callback = "";
    this.htmlContent = "";
	
	this.show = function() {
			
		if(this.windowConfig) {
		
            // Make non-modal
			if(!this.windowConfig.isModal) $(".rbwindow_screen").click(this.remove);
			
			// Custom Window Width
			if(this.windowConfig.width) this.windowWidth = this.windowConfig.width;

			// Custom Window Height
			if(this.windowConfig.height) this.windowHeight = this.windowConfig.height;
			
            // Ajax Content URL
			if(this.windowConfig.url) this.url = this.windowConfig.url;
            
            // Hard Coded Window Content
            if(this.windowConfig.htmlContent) this.htmlContent = this.windowConfig.htmlContent;
			
			// need to be able to return something when the window is closed!
			// could pass in an onclose function?
			if(this.windowConfig.callback) {
				if(typeof(this.windowConfig.callback) == "function") {
					this.callback = this.windowConfig.callback;
				}
			}
			
		} else {
			// No Config Supplied - Add Default Actions
			
			// Make non-modal
			$(".rbwindow_screen").click(this.remove);
		}
		
		// Generate main container
		var container = draw(this);
	
		// Add elements to DOM
		var body = document.getElementsByTagName("body")[0];
		var screen = document.createElement('div');
		screen.className = 'rbwindow_screen';
		body.appendChild(screen);
		screen.appendChild(container);
        
        if(this.url.length > 0) {
			container.innerHTML = ajax_UpdateElement(this.url, 'rbwindow');
        }
        else if(this.htmlContent.length > 0) {
            container.innerHTML = this.htmlContent;
            
		} else {
			container.innerHTML = '<p class="info">Content not found!</p>';
		}
		
		// Add Actions...
		$(".rbwindow_screen").click(this.remove);
		
		// Remove scroll on main window
		$("html").css("overflow","hidden");
		$("body").css("overflow","hidden");
	};
	
	var draw = function(container) {
		
		// Create element
		var window = document.createElement('div');
		
		// Generate Dynamic CSS
		lsStyle = 'height:' + container.windowHeight + 'px;'
		lsStyle+= 'width:' + container.windowWidth + 'px;'
		
		var windowWidth = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth;
		var windowHeight = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight;
		var liLeft = (windowWidth - container.windowWidth) / 2;
		var liTop = (windowHeight - container.windowHeight) / 2;
		lsStyle+= 'top:' + liTop + 'px;';
		lsStyle+= 'left:' + liLeft + 'px;';
		lsStyle+= 'position:fixed;';
		window.setAttribute('style', lsStyle);
		
		// Apply Attributes
		window.className = 'rbwindow';
		window.id = 'rbwindow';
		
		return window;
	};
	
	// Function to remove everything!
	this.remove = function(e) {
		if(e.target !== this) return;
		$(".rbwindow_screen").remove();
		$("html").css("overflow","visible");
		$("body").css("overflow","visible");
	};
	
}