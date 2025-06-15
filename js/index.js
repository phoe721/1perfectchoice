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

	// Form 1	
	var form2 = $('#form2');
	form2.validate({
	    rules: {
	        input: {
	            required: true,
				pattern: /^[A-Za-z0-9\-+x\/\._\&]+$/
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
			$('#task2').append('<option value=' + key + '>' + value + '</option>');
		});
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

	// Bind Enter Key
	$(document).ready(function() {
		// Hide Output 
		$('#box1').hide();
		$('#box2').hide();

		// Disable this button until upload
		$('#run').attr('disabled', true);

		// Reset
		$('#reset').click(function() {
			location.reload();
		});

		// On change trigger check
		/*
	    $('input').keyup(function(event) {
  			if (event.which === 17) {
    			event.preventDefault();
				$('#check').trigger('click');
			}
    	});
		*/

		// Upload button
		$('#upload_button').click(function() {
			$('#box1').toggle('slow');
		});

		// Check button
		$('#check_button').click(function() {
			$('#box2').toggle('slow');
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

		// Show Dropzone 
		$('#dropzone').click(function() {
			$("div#box3").toggle('slow');
		});
	});

	// Check button
	$('#check').click(function() {
		$('#error').text('');
		$('#warning').text('');
		if (form2.valid()) {
			var input = $('#input').val();
			//input = input.replace(/-local.*/gi, '').replace(/\+/, '-').replace(/^(SR)([0-9]+)/, 'SR-$2');
			input = input.replace(/-local.*/gi, '').replace(/^(SR)([0-9]+)/, 'SR-$2');
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
						$('#product_img').attr('src', data.img_url).prop('alt', data.sku);
						$('#product_img_wb').attr('src', data.img_wb_url).prop('alt', data.sku);
						$('#vendor').val(data.vendor);
						$('#sku').attr('href', data.query_url).text(data.sku);
						$('#asin').attr('href', data.asin_url).text(data.asin);
						$('#upc').val(data.upc);
						$('#discontinued').val(data.status);
						$('#set_list').val(data.set_list.join());
						$('#item_type').val(data.item_type);
						$('#cost').val(data.cost);
						$('#cost_updated_time').val(data.cost_updated_time);
						$('#unit').val(data.unit);
						$('#qty').val(data.quantity);
						$('#qty_updated_time').val(data.inventory_updated_time);
						$('#title').val(data.title);
						$('#color').val(data.color);
						$('#material').val(data.material);
						$('#features').val(data.features.join());
						$('#description').val(data.description);
						$('#weight').val(data.weight);
						$('#dimension').val(data.dimension.join());
						$('#box_count').val(data.boxCount);
						$('#pg_dimension').val(data.packageDimension.join());
						$('#pg_weight').val(data.packageWeight.join());
						$('#total_pg_weight').val(data.totalPackageWeight);
						$('#manufacturing_country').val(data.manufacturing_country);
						$('#note').val(data.note);
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
						$('input').focus(function() {
							var length = $(this).val().length;
							if (length > 100) length = 100;
							$(this).attr('size', length);
						});
						$('input').blur(function() {
							//$(this).attr('size', '20');
							var length = $(this).val().length;
							if (length > 100) length = 100;
							$(this).attr('size', length);
						});
					}
				}
			});
		}
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
