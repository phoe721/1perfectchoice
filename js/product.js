$(document).ready(function() {	
	// Form validator
	jQuery.validator.setDefaults({
	    debug: false,
	    success: 'valid'
	});

	// Form 2	
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

						// Change Image	
						$('#product_img').click(function() {
							var formData3 = new FormData();
							formData3.append('input', input);
							formData3.append('product_img', data.img_url);

							$.ajax({
								url: 'script/changeImage.php',
								data: formData3,
								type: 'POST',
								contentType: false,
								processData: false,
								dataType: 'json',
								success: function(data) {
									if (data.error == 'SKU not found!') {
										$('#error').text(data.error);
									} else {
										$('#product_img').attr('src', data.img_url).prop('alt', data.sku);
									}
								}
							});
						});
					}
				}
			});
		}
	});	

});
