var Amasy = {};
Amasy.createXHR = function(url, options){
	var xhr = false;
	//does browser support AJAX?
	if(window.XMLHttpRequest){
		xhr = new XMLHttpRequest();
	}
	if(xhr){
		options = options || {};
		options.method = options.method || "GET";
		xhr.onreadystatechange = function(){
			if((xhr.readyState == 4) && (xhr.status == 200 || xhr.status == 304)){
				if(options.complete){
					//all js functions have a call()
					//the 1st argument specifies what 'this' refers to 
					options.complete.call(xhr, JSON.parse(xhr.responseText));
				}
			}
		};
		xhr.open(options.method, url, true);
		return xhr;
	} else {
		return false;
	}	
};
Amasy.ajax = function(url, options){
	var xhr = Amasy.createXHR(url, options);
	if(xhr){
		xhr.send(null);
	}
};