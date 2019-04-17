$(document).ready(function() {	
	// Form validator
	jQuery.validator.setDefaults({
	    debug: false,
	    success: 'valid'
	});

	// Form 1	
	var form = $('#form');
	form.validate({
	    rules: {
	        sku: {
	            required: true,
				pattern: /^[A-Z]+-[A-Z0-9-+x. \/*]+$/
	        }
	    }
	});

	// Form 2	
	var form2 = $('#form2');
	form2.validate({
	    rules: {
	        file: {
	            required: true
	        }
	    }
	});

	// Disable this button until upload
	$('#run').attr("disabled", true);

	// Get UID	
	$.post('script/getUID.php', {getUID: 'yes'}, function(id) {
		if (typeof id != 'undefined') {
			$('#uid').val(id);
		} else {
			console.log('Unable to get ID for this process!');
		}
	});

	// Validate file	
	$('#file').change(function() {
		$('#output').html('');
	    if (!form2.valid()) {
	        console.log('Invalid File!');
	    }
	});

	// Run queue	
	$('#run').click(function() {
		$.post('script/runQueue.php');
	});

	// Reset buttons
	$('#reset, #reset2').click(function() {
		location.reload();
	});

	// Check button
	$('#check').click(function() {
		$('#output').html('');
		if (form.valid()) {
			var sku = $('#sku').val();
			var script = 'script/checkItem.php';
			var formData = new FormData();
			formData.append('sku', sku);

			$.ajax({
				url: script,
				data: formData,
				type: 'POST',
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(output) {
					$('#output').append(output + '<br>');
				}
			});
		}
	});	

	// Upload button
	$('#upload').click(function() {
		if (form2.valid()) {
			$f1 = $('#file');
			var uid = $('#uid').val();
			var task = $('#task2').val();
			var formData = new FormData();
			formData.append('uid', uid);
			formData.append('task', task);
			if($f1.val()) formData.append('file', $f1.get(0).files[0]);
	
			// Upload file	
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
	
			// Enable run button
			$('#run').attr("disabled", false);
	
			// Wait for result
			var time = 10000;
			var prev = cur = '';
			var check = setInterval(function(){ 
				$.post('script/getStatus.php', {uid: uid}, function(result) { 
					cur = result.status;
					if (cur.match(/Done/)) {
						$('#output').html('').append(result.status + ' ');
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
});
