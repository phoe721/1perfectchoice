jQuery.validator.setDefaults({
    debug: false,
    success: "valid"
});

var form = $('#form');
form.validate({
    rules: {
        file1: {
            required: true,
            accept: 'text/plain',
            extension: 'txt|csv'
        },
        file2: {
            required: true,
            accept: 'text/plain',
            extension: 'txt|csv'
        },
        file3: {
            required: true,
            accept: 'text/plain',
            extension: 'txt|csv'
        }
    }
});

$('#file1').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
    }
});

$('#file2').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
    }
});

$('#file3').change(function() {
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
	$('#loading').css('display', 'block');
	$f1 = $('#file1');
	$f2 = $('#file2');
	$f3 = $('#file3');
	var formData = new FormData();
	formData.append('uid', uid);
	if($f1.val()) formData.append('file1', $f1.get(0).files[0]);
	if($f2.val()) formData.append('file2', $f2.get(0).files[0]);
	if($f3.val()) formData.append('file3', $f3.get(0).files[0]);

	$.ajax({
		url: 'script/getStock.php',
		data: formData,
		type: 'POST',
		contentType: false,
		processData: false,
		success: function() {
			$('#loading').css('display', 'none');
			$('#output').html('File uploaded!<br>');
		}
	});

	var time = 10000;
	var prev = cur = '';
	var check = setInterval(function(){ 
		$.post('script/getStatus.php', {uid: uid}, function(result) { 
			cur = result.status;
			if (cur.match(/Done/)) {
				$('#output').append(result.status + "<br>");
				for (var i = 0; i < result.link.length; i++) {	
		    		$('#output').append('<a href="' + result.link[i] + '" target="_blank" download>' + result.link[i] + '</a><br>');
				}
				clearInterval(check);
			} else if (prev != cur) {
				$('#output').append(result.status + "<br>");
				prev = cur;
			}
		}, "json");
	}, time);
});


