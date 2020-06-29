var config = {
	"loop": 1000,
	"offline": {
		"luna_status": 30,
		"luna_local":  120,
		"luna_public": 120,

		"chrysalis_file_local":    120,
		"chrysalis_file_public":   120,
		"chrysalis_stream_local":  120,
		"chrysalis_stream_public": 120,
		"chrysalis_ann":           120,

		"rarity_local":  120,
		"rarity_public": 120,
        "raritush": 60,

		"tantabus_local":  120,
		"tantabus_public": 120,

		"varipass": 120,
		"celly":    180,
		"chryssy":  180,
		"dashie":   180,
		"twilight": 1800,

		"tradfri": 120,
		"lulu":    5,
		"sparkle": 600,
        "exclaml": 60,
        "obs": 60
	}
}

function getTimeStringSimple(time) {
    var string = "";

    if (time.days != null && time.days != 0) {
        string += time.days + "d ";
    }

    if (time.hours != null && time.hours != 0) {
        string += time.hours + "h ";
    }

    if (time.minutes != null && time.minutes != 0) {
        string += time.minutes + "m ";
    }

    if (time.seconds != null) {
        string += time.seconds + "s";
    }

    return string;
}

function fetchStatus(callback) {
	var xhr = new XMLHttpRequest();

	var url = "https://status.thorinair.net/api/";
	xhr.open("GET", url, true);

	xhr.onreadystatechange = function() { 
	    if (xhr.readyState == 4 && xhr.status == 200) {
	        var response = JSON.parse(xhr.responseText);
	        callback(response);
	    }
	}
	xhr.onerror = function() {
	    console.log("Error!");
	    window.setTimeout(function() {
	    	xhr.abort();
	    }, 1000);
	}
	xhr.ontimeout = function() {
	    console.log("Timeout!");
	    window.setTimeout(function() {
	    	xhr.abort();
	    }, 1000);
	}

	xhr.send();
}

function loopStatus() {
	
	fetchStatus(function (r) {
		var list = r.data.split(",");
		list.forEach(function(l, i) {
			var parts = l.split(":");
			if (config.offline[parts[0]] != undefined) {

				var status = "unknown";
				if (parts[1] != "undefined") {
					var t = parseInt(parts[1]);

					if (t <= config.offline[parts[0]])
						status = "online";
					else
						status = "offline";

			        var diff = t;
			        var time = {};
			        time.seconds = Math.floor(diff % 60);
			        diff = Math.floor(diff / 60);
			        time.minutes = Math.floor(diff % 60);
			        diff = Math.floor(diff / 60);
			        time.hours = Math.floor(diff % 24);
			        time.days = Math.floor(diff / 24);

					document.getElementById("time_" + parts[0]).innerHTML = getTimeStringSimple(time);
				}
				else {
					document.getElementById("time_" + parts[0]).innerHTML = "Unknown";
				}
		     
				document.getElementById("time_" + parts[0]).className = status;
				document.getElementById("name_" + parts[0]).className = status;
			}
		});

		var status = "offline";
		var t = parseInt(r.age);
		if (t <= config.offline.luna_status)
			status = "online";

        var diff = t;
        var time = {};
        time.seconds = Math.floor(diff % 60);
        diff = Math.floor(diff / 60);
        time.minutes = Math.floor(diff % 60);
        diff = Math.floor(diff / 60);
        time.hours = Math.floor(diff % 24);
        time.days = Math.floor(diff / 24);

		document.getElementById("time_luna_status").innerHTML = getTimeStringSimple(time);
		document.getElementById("time_luna_status").className = status;
		document.getElementById("name_luna_status").className = status;



	});

	window.setTimeout(function() {
		loopStatus();
	}, config.loop);
}

function init() {
	loopStatus();
}