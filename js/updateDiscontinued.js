jQuery.validator.setDefaults({
    debug: false,
    success: "valid"
});

var form = $('#form');
form.validate({
    rules: {
        file1: {
            required: true
        }
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
		var formData = new FormData();
		if($f1.val()) formData.append('file1', $f1.get(0).files[0]);
		formData.append('uid', uid);
	
		$.ajax({
			url: 'script/updateDiscontinued.php',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(result) {
				$('#output').append(result.status + '<br>');
			}
		});

		var time = 5000;
		var prev = cur = '';
		var check = setInterval(function(){ 
			$.post('script/getStatus.php', {uid: uid}, function(result) { 
				cur = result.status;
				if (cur.match(/Done/)) {
					$('#output').html('').append(result.status + "<br>");
					clearInterval(check);
				} else if (prev != cur) {
					$('#output').html('').append(result.status + "<br>");
					prev = cur;
				}
			}, "json");
		}, time);
	}
});


