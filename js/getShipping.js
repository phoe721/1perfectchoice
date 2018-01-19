jQuery.validator.setDefaults({
    debug: false,
    success: "valid"
});

var form = $('#form');
form.validate({
    rules: {
        sku: {
			required: true,
			number: true
		},
        cost: {
			required: true,
			number: true
		},
        weight: {
			required: true,
			number: true
		},	
        length: {
			required: true,
			number: true
		},
        width: {
			required: true,
			number: true
		},
        height: {
			required: true,
			number: true
		}
    }
});

$('#reset').click(function() {
	$(':text').val('');
	$('input.error').css({"border-color":"black","border-width":"1px","border-style":"solid"}); 
	$('#sku-error').html('').removeClass('error valid');
	$('#cost-error').html('').removeClass('error valid');
	$('#weight-error').html('').removeClass('error valid');
	$('#length-error').html('').removeClass('error valid');
	$('#width-error').html('').removeClass('error valid');
	$('#height-error').html('').removeClass('error valid');
	$('#output').html('');
});

$('#submit').click(function() {
    if (form.valid()) {
		var formData = new FormData();
		formData.append('sku', $('#sku').val());
		formData.append('cost', $('#cost').val());
		formData.append('weight', $('#weight').val());
		formData.append('length', $('#length').val());
		formData.append('width', $('#width').val());
		formData.append('height', $('#height').val());

		$.ajax({
			url: 'script/getShipping.php',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(result) {
				$('#output').append(result.status + '<br>');
			}
		});
	}
});

