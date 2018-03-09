/************************
 Make an POST ajax call
************************/
function ajax_PostRequest(url, request, successFunction) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    if (window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
    // code for IE6, IE5
    else xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            successFunction(xmlhttp.responseText);
        }
    };

    // Send XML Request
    xmlhttp.open("POST",url,true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	//alert(typeof request);
    xmlhttp.send(request);
}

function encapsulateForm(form)
{
	// need to convert form fields to a string which can be passed into a post request
	// e.g. formfield1=xuy&formfield2=tqjei
	
	var querystring = "";
	
	$(form).children('input, select').each(function() {
		if(querystring.length == 0) {
			querystring+= $(this).attr('name') + "=" + $(this).val();
		}
		else {
			querystring+= "&" + $(this).attr('name') + "=" + $(this).val();
		}
	});
    	
	return querystring;
}

function ajax_PostFile(url, request, elem) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    if (window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
    // code for IE6, IE5
    else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
           document.getElementById(elem).innerHTML = xmlhttp.responseText;
        }
    };

    // Send XML Request
    xmlhttp.open("POST", url, true);
    //.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	//xmlhttp.setRequestHeader("Content-type", "multipart/form-data"); 
	
	//alert(typeof request);
    xmlhttp.send(request);
}


/************************
 Make an GET ajax call
************************/
function ajax_GetRequest(url) {
	$.get(url, function(data) {
		return data;
	});
}

function ajax_GetJSON(url) {

}
function ajax_GetXML(url) {

}

function ajax_UpdateElement_jQuery(url,$elem) {
	$.get(url, function(data) {
		$elem.html(data);
	});
}

function ajax_UpdateElement(url, elementID) {
	$('#' + elementID).html('loading....');
	$.get(url, function(data) {
		$('#' + elementID).html(data);
	});
}

/************************
 Update an element on the page with an ajax call
************************/
function zzzajax_UpdateElement(url,elem) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest();
    // code for IE6, IE5
    else xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    
    xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==3) {
			
		}
        if (xmlhttp.readyState==4 && xmlhttp.status==200 ) {
            //document.getElementById("elem").innerHTML = ;
        }
    };

    // Send XML Request
    xmlhttp.open("GET",url,true);
    xmlhttp.send();
}