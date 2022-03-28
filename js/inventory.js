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
	$(document).ready(function() {
	    $('input').keyup(function(event) {
  			if (event.which === 17) {
    			event.preventDefault();
				$('#check').trigger('click');
    		}
    	});
	});

	// Check button
	$('#check').click(function() {
		$('#error').text('');
		$('#warning').text('');
		if (form.valid()) {
			var input = $('#input').val();
			input = input.replace(/-local.*/gi, '').replace(/\+/, '-').replace(/^SR0/, 'SR-0');
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
						$('#vendor').val(data.vendor);
						$('#sku').attr('href', data.query_url).text(data.sku);
						$('#qty').val(data.quantity);
						$('#qty_updated_time').val(data.inventory_updated_time);
					}
				}
			});
		}
	});	
});
