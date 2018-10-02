jQuery.validator.setDefaults({
    debug: false,
    success: 'valid'
});


var form = $('#form');
form.validate({
    rules: {
        file: {
            required: true
        }
    }
});

$.post('script/getUID.php', {getUID: 'yes'}, function(id) {
	if (typeof id != 'undefined') {
		$('#uid').val(id);
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
		$f1 = $('#file');
		var uid = $('#uid').val();
		var task = $('#task').val();
		var formData = new FormData();
		formData.append('uid', uid);
		formData.append('task', task);
		if($f1.val()) formData.append('file', $f1.get(0).files[0]);
	
		$.ajax({
			url: 'script/upload.php',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(output) {
				$('#output').append(output + '<br>');
			}
		});

		var time = 10000;
		var prev = cur = '';
		var check = setInterval(function(){ 
			$.post('script/getStatus.php', {uid: uid}, function(result) { 
				cur = result.status;
				if (cur.match(/Done/)) {
					$('#output').html('').append(result.status + '<br>');
			    	$('#output').append('<a href="' + result.link + '" target="_blank" download>result.txt</a><br>');
					clearInterval(check);
				} else if (prev != cur) {
					$('#output').append(result.status + '<br>');
					prev = cur;
				}
			}, 'json');
		}, time);
	}
});
