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
				pattern: /^[A-Z0-9\-+x\/]+$/
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
	$('#run').attr('disabled', true);

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
			$('#task2').append('<option value=' + key + '>' + value + '</option>');
		});
	});

	// Show Dropzone 
	$('#dropzone').click(function() {
		$("div#box3").toggle('slow')
	});

	// Dropzone
	$('div#box3').addClass('dropzone').dropzone({ 
		url: 'script/uploadImg.php', 
		paramName: 'file',
		maxFilesize: 2,
		init: function() {
			this.on('sending', function(file, xhr, formData) {
				var uid = $('#uid').val();
				formData.append('uid', uid);
		    });
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
	
	// Bind Enter Key
	$(document).bind('keypress', function(e) {
		if(e.keyCode==13){
			$('#check').trigger('click');
        }
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
				success: function(data) {
					$('#output').append('<div style="float: left; margin: 5px;">' + 
					'<img src="' + data.img_url + '" width="300px" alt="' + data.sku + '" </div>');
					$('#output').append('<div style="float: left; margin: 5px;">' +
					'Vendor: ' + data.vendor + '<br>' +
					'SKU: <a href="' + data.query_url + '" target="_blank">' + data.sku + '</a><br>' +
					'ASIN: <a href="https://www.amazon.com/dp/' + data.asin + '" target="_blank">' + data.asin + '</a><br>' +
					'UPC: <input type="text" value="' + data.upc + '"><br>' +
					'Discontinued: <input type="text" value="' + data.status + '"><br>' +
					'Set List: <input type="text" value="' + data.set_list + '"><br>' +
					'Cost: <input type="text" value="' + data.cost + '"> (' + data.updated_time + ')<br>' +
					'Unit: <input type="text" value="' + data.unit + '"><br>' +
					'Quantity: <input type="text" value="' + data.quantity + '"><br>' +
					'Title: <input type="text" value="' + data.title + '"><br>' +
					'Color: <input type="text" value="' + data.color + '"><br>' +
					'Material: <input type="text" value="' + data.material + '"><br>' +
					'Features: <input type="text" value="' + data.features.join() + '"><br>' +
					'Description: <input type="text" value="' + data.description + '"><br>' +
					'Weight: <input type="text" value="' + data.weight + '"><br>' +
					'Dimension: <input type="text" value="' + data.dimension.join() + '"><br>' +
					'Box Count: <input type="text" value="' + data.boxCount + '"><br>' + 
					'Package Dimensions: <input type="text" value="' + data.packageDimension.join() + '"><br>' + 
					'Package Weight: <input type="text" value="' + data.packageWeight.join() + '"><br>' + 
					'Total Package Weight: <input type="text" value="' + data.totalPackageWeight + '"><br>' + 
					'</div>');
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
