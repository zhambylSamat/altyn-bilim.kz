$date = '';

self.onmessage = function(e){
	$parts = e.data.split(":");
	$date = new Date();
	$date.setHours(parseInt($parts[0]));
	$date.setMinutes(parseInt($parts[1])-15);
	$date.setSeconds(0);
	timer();
}

function timer(){
	if(new Date()<=$date){
		setTimeout("timer()", 1000);
	}
	else {
		postMessage("show");
	}
}