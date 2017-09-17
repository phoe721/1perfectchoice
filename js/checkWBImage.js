jQuery.validator.setDefaults({
    debug: false,
    success: "valid"
});

var form = $('#form');
form.validate({
    rules: {
		server: {
            required: true
		},
		user: {
        	required: true
		},
		pass: {
        	required: true
		},	
		directory: {
			required: true
		}
    }
});

var uid = '';
$.post('script/getUID.php', {getUID: "yes"}, function(id) {
	if (typeof id != 'undefined') {
		uid = id;
	} else {
		console.log('Unable to get ID for this process!');
	}
});

var server = user = pass = directory = '';
$('#pass').change(function() {
	server = $('#server').val();
	user = $('#user').val();
	pass = $('#pass').val();

	if (server && user && pass) {
		$.post('script/checkWBImage.php', {server: server, user: user, pass: pass}, function(result) {
			$('#output').html('');
			$('#output').append(result.status + '<br>');
			for (var i = 0; i < result.files.length; i++) {
				$('#directory').append($('<option></option>').attr("value", result.files[i]).text(result.files[i])); 
			}
		}, 'json');
	}
});

var selected = '';
$('#directory').change(function() {
	selected = $('#directory').val();	
	$.post('script/checkWBImage.php', {uid: uid, server: server, user: user, pass: pass, selected: selected}, function(result) {
		$('#output').append(result.status + '<br>');
	}, 'json');

	var time = 10000;
	var prev = cur = '';
	var check = setInterval(function(){ 
		$.post('script/getStatus.php', {uid: uid}, function(result) { 
			cur = result.status;
			if (cur.match(/Done/)) {
				for (var i = 0; i < result.link.length; i++) {	
		    		$('#output').append('<a href="' + result.link[i] + '" target="_blank" download>' + result.link[i] + '</a><br>');
				}
				clearInterval(check);
			} else if (prev != cur) {
				$('#output').html('').append(result.status + "<br>");
				prev = cur;
			}
		}, "json");
	}, time);
});

