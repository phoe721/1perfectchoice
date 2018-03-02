jQuery.validator.setDefaults({
    debug: false,
    success: "valid"
});

var form = $('#form');
form.validate({
    rules: {
        sku: {
			required: true
		}
    }
});

$('#reset').click(function() {
	$(':text').val('');
	$('input.error').css({"border-color":"black","border-width":"1px","border-style":"solid"}); 
	$('#sku-error').html('').removeClass('error valid');
	$('#output').html('');
});

$('#submit').click(function() {
    if (form.valid()) {
		var formData = new FormData();
		formData.append('sku', $('#sku').val());

		$.ajax({
			url: 'script/checkDiscontinued.php',
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

