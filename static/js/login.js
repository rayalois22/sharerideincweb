$("document").ready(function(){
	//change the language accross the site.
	$("#lang-select").on('change', function(){
		var lang = $(this).val();
		var flang = "index.php?lang=" + lang;
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(){
			if((xhr.readyState == 4) && (xhr.status == 200 || xhr.status == 304)){
				location.reload(true);
			}
		};
		xhr.open("GET", flang, true);
		xhr.send(null);
	});
	//change the body 
	$("body").css({
		"background-color":"#009688"
	});
	$("legend").css({
		"color":"#434a52"
	});
	$("input[type='submit']").css({});
	$("input[type='submit']").hover(function(){
		$("input[type='submit']").css({});
	});
	$("fieldset").css({
		"border":"0px",
		"text-align":"center"
	});
	$("select").css({
		"background-color":"#009688",
		"border-color":"#434a52",
		"color":"#434a52",
		"width":"100%"
	});
});