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
				pattern: /^[A-Za-z0-9\-+x\/\._\&]+$/
	        }
	    }
	});

	$('input').focus(function() {
		var length = $(this).val().length;
		if (length > 100) length = 100;
		$(this).attr('size', length);
	});

	$('input').blur(function() {
		$(this).attr('size', '20');
	});

	// Get UID	
	$.post('script/getUID.php', {getUID: 'yes'}, function(id) {
		if (typeof id != 'undefined') {
			$('#uid').val(id);
		} else {
			console.log('Unable to get ID for this process!');
		}
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
					}
				}
			});
		}
	});	
});
