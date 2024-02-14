$(document).ready(function() {	
	// Form validator
	jQuery.validator.setDefaults({
	    debug: false,
	    success: 'valid'
	});

	// Form	
	var form = $('#form');
	form.validate({
	    rules: {
	        file: {
	            required: true
	        }
	    }
	});

	// Get UID	
	$.post('script/getUID.php', {getUID: 'yes'}, function(id) {
		if (typeof id != 'undefined') {
			$('#uid').val(id);
		} else {
			console.log('Unable to get ID for this process!');
		}
	});

	// Get Task List
	$.getJSON('script/getTaskList.php', function(data){
		$.each(data, function(key, value){
			if (value.match(/Inventory/)) {
				$('#task2').append('<option value=' + key + '>' + value + '</option>');
			}
		});
	});

	// Bind Enter Key
	$(document).ready(function() {
		// Hide Output 
		$('#box1').hide();

		// Disable this button until upload
		$('#run').attr('disabled', true);

		// Reset
		$('#reset').click(function() {
			location.reload();
		});

		// Upload button
		$('#upload_button').click(function() {
			$('#box1').toggle('slow');
		});

		// Run queue	
		$('#run').click(function() {
			$.post('script/runQueue.php');
		});

		// Check inventory Percentage
		$('#inventory_percentage').click(function() {
			$.post('script/checkInventoryPercentageByVendor.php', function(data) {
				alert(data);
			});
		});

		// Clear inventory
		$('#clear_inventory').click(function() {
			if (confirm("Are you sure you want to clear inventory?") == true) {
				$.post('script/truncateInventory.php');
				alert("Inventory cleared!");
			} else {
				alert("Inventory not cleared!");
			}
		});

		// Validate file	
		$('#file').change(function() {
			$('#output2').html('');
		    if (!form.valid()) {
		        console.log('Invalid File!');
		    }
		});
	});

	// Upload button
	$('#upload').click(function() {
		if (form.valid()) {
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
					$('#output2').append(output + '<br>');
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
						$('#output2').html('').append(result.status + ' ');
				    	$('#output2').append('<a href="' + result.link + '" target="_blank" download>result.txt</a><br>');
						clearInterval(check);
					} else if (prev != cur) {
						$('#output22').append(result.status + '<br>');
						prev = cur;
					}
				}, 'json');
			}, time);
		}
	});
});
