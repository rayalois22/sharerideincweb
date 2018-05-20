/*
$("document").ready(function(){
	$("#cbgroup").click(function(){
		var cb = [];
		$("#cbgroup input[type='checkbox']").each(function(){
			//cb.push($(this).attr('name'));
			//cb.push($(this));
		});
	});
	$(".main_content").css({
        
    });
});
*/
/*
(function(){
	var link = document.getElementById("#json");
	link.onclick = function(){
		Amasy.ajax('./?json', {
			method:"GET",
			complete: function(response){
				var body = document.getElementsByTagName("body")[0];
				var json = response;
				alert(json);
			}
		});
		return false;
	}
})();
*/

$("document").ready(function(){
	Clock.startTime();//display the clock
	$("#clock").css({});
});

function toggle(t){
	$("t + input").checked = !t.checked;
}

function vehicleSelector(sel){
	var i = sel.selectedIndex;
	var vehicle = sel.options[i].value;
	var vehicle = vehicle.split("||");	
	document.getElementById("rvreg").value = vehicle[0];
	document.getElementById("rvmod").value = vehicle[1];
	document.getElementById("rvcap").value = vehicle[2];	
}