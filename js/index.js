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
	        input: {
	            required: true,
				pattern: /^[A-Za-z0-9\-+x\/\.]+$/
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
		$("div#box3").toggle('slow');
	});

	// Dropzone
	Dropzone.options.box3 = {
		url: 'script/uploadImg.php', 
		paramName: 'file',
		maxFilesize: 2,
		init: function() {
			this.on('sending', function(file, xhr, formData) {
				var uid = $('#uid').val();
				formData.append('uid', uid);
		    });
		}
	};

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
	$('#reset').click(function() {
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
		$('#error').text('');
		$('#warning').text('');
		if (form.valid()) {
			var input = $('#input').val();
			input = input.replace(/-local.*/gi, '').replace(/\+/, '-');
			var formData = new FormData();
			formData.append('input', input);

			$.ajax({
				url: 'script/checkItem.php',
				data: formData,
				type: 'POST',
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(data) {
					if (data.error == 'SKU not found!') {
						$('#error').text(data.error);
					} else {
						$('#warning').text(data.warning);
						$('#output').append('<div style="float: left; margin: 5px;">' + 
						'<img src="' + data.img_url + '" width="300px" alt="' + data.sku + '"><br>' +
						'<img src="' + data.img_wb_url + '" width="300px" alt="' + data.sku + '">');
						$('#output').append('<div style="float: left; margin: 5px;"><table>' +
						'<tr><td>Vendor</td><td>' + data.vendor + '</td></tr>' +
						'<tr><td>SKU</td><td><a href="' + data.query_url + '" target="_blank">' + data.sku + '</a></td></tr>' +
						'<tr><td>ASIN</td><td><a href="https://www.amazon.com/dp/' + data.asin + '" target="_blank">' + data.asin + '</a></td></tr>' +
						'<tr><td>UPC</td><td><input type="text" id="upc" value="' + data.upc + '"></td></tr>' +
						'<tr><td>Status</td><td><input type="text" id="discontinued" value="' + data.status + '"></td></tr>' +
						'<tr><td>Set List</td><td><input type="text" id="set_list" value="' + data.set_list.join() + '"></td></tr>' +
						'<tr><td>Item Type</td><td><input type="text" id="item_type" value="' + data.item_type + '"></td></tr>' +
						'<tr><td>Cost</td><td><input type="text" id="cost" value="' + data.cost + '"></td></tr>' +
						'<tr><td>Updated Time</td><td>' + data.cost_updated_time + '</td></tr>' +
						'<tr><td>Unit</td><td><input type="text" id="unit" value="' + data.unit + '"></td></tr>' +
						'<tr><td>Quantity</td><td><input type="text" id="qty" value="' + data.quantity + '"></td></tr>' +
						'<tr><td>Updated Time</td><td>' + data.inventory_updated_time + '</td></tr>' +
						'<tr><td>Title</td><td><input type="text" id="title" value="' + data.title + '"></td></tr>' +
						'<tr><td>Color</td><td><input type="text" id="color" value="' + data.color + '"></td></tr>' +
						'<tr><td>Material</td><td><input type="text" id="material" value="' + data.material + '"></td></tr>' +
						'<tr><td>Features</td><td><input type="text" id="features" value="' + data.features.join() + '"></td></tr>' +
						'<tr><td>Description</td><td><textarea id="description">' + data.description + '</textarea></td></tr>' +
						'<tr><td>Weight</td><td><input type="text" id="weight" value="' + data.weight + '"></td></tr>' +
						'<tr><td>Dimension</td><td><input type="text" id="dimensions" value="' + data.dimension.join() + '"></td></tr>' +
						'<tr><td>Box Count</td><td>' + data.boxCount + '</td></tr>' + 
						'<tr><td>Package Dimension</td><td><input type="text" id="pg_dimension" value="' + data.packageDimension.join() + '"></td></tr>' + 
						'<tr><td>Package Weight</td><td><input type="text" id="pg_weight" value="' + data.packageWeight.join() + '"></td></tr>' + 
						'<tr><td align="center">Total Package Weight</td><td>' + data.totalPackageWeight + '</td></tr>' + 
						'</table></div>');
						$('input').focus(function() {
							var length = $(this).val().length;
							if (length > 100) length = 100;
							$(this).attr('size', length);
						});
						$('input').blur(function() {
							$(this).attr('size', '20');
						});
						$('input, textarea').change(function() {
							var field = $(this).attr('id');
							var value = $(this).val();
							var formData2 = new FormData();
							formData2.append('sku', data.sku);
							formData2.append('field', field);
							formData2.append('value', value);
	
							$.ajax({
								url: 'script/update.php',
								data: formData2,
								type: 'POST',
								contentType: false,
								processData: false,
								dataType: 'json',
								success: function(result) {
									console.log(result);
								}
							});
						});
					}
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
