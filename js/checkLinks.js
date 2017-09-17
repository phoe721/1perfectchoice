jQuery.validator.setDefaults({
    debug: false,
    success: 'valid'
});


var form = $('#form');
form.validate({
    rules: {
        file: {
            required: true,
            accept: 'text/plain',
            extension: 'txt|csv'
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

$('#file').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
    }
});


$('#upload').click(function() {
    if (form.valid()) {
		var formObj = $('#form')[0];
		var formData = new FormData(formObj);
		formData.append('uid', uid);

		$.ajax({
			url: 'script/checkLinks.php',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
			success: function() {
				$('#output').html('File uploaded!<br>');
			}
		});

		var time = 5000;
		var prev = cur = '';
		var check = setInterval(function(){ 
			$.post('script/getStatus.php', {uid: uid}, function(result) { 
				cur = result.status;
				if (cur.match(/Done/)) {
					$('#output').html('').append(result.status + "<br>");
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
	}
});

