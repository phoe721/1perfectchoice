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
				pattern: /^[A-Za-z0-9\-+x\/]+$/
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
		if (form.valid()) {
			var input = $('#input').val();
			input = input.replace(/-local.*/gi, "");;
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
					$('#product_img').attr('src', data.img_url).prop('alt', data.sku);
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
					$('#img_dim').val(data.img_dim);

					$('input').change(function() {
						var sku = $('#sku').val();
						var field = $(this).attr('id');
						var value = $(this).val();
						var formData2 = new FormData();
						formData2.append('sku', sku);
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
			});
		}
	});	
});
