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
		},
        file1: {
            required: true
        }
    }
});

var server = user = pass = directory = '';
$('#pass').change(function() {
	server = $('#server').val();
	user = $('#user').val();
	pass = $('#pass').val();

	if (server && user && pass) {
		$.post('script/updateInventory.php', {server: server, user: user, pass: pass}, function(result) {
			$('#output').html('');
			$('#output').append(result.status + '<br>');
			for (var i = 0; i < result.files.length; i++) {
				$('#directory').append($('<option></option>').attr("value", result.files[i]).text(result.files[i])); 
			}
		}, 'json');
	}
});

$('#file1').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
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

$('#upload').click(function() {
	if (form.valid()) {
		$f1 = $('#file1');
		directory = $('#directory').val();
		var formData = new FormData();
		formData.append('uid', uid);
		formData.append('server', server);
		formData.append('user', user);
		formData.append('pass', pass);
		formData.append('directory', directory);
		if($f1.val()) formData.append('file1', $f1.get(0).files[0]);
	
		$.ajax({
			url: 'script/updateInventory.php',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(result) {
				$('#output').append(result.status + '<br>');
			}
		});
	}
});


