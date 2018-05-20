var Clock = {};
Clock.startTime = function(){
	var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = Clock.checkTime(m);
    s = Clock.checkTime(s);
    document.getElementById('clock').innerHTML = h + ":" + m + ":" + s;
	setTimeout(Clock.startTime, 500);
	//var t = setTimeout(Clock.startTime, 500);
};
Clock.checkTime = function(time){
	if (time < 10) {time = "0" + time};  // add zero in front of numbers < 10
    return time;
};