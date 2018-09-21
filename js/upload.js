jQuery.validator.setDefaults({
    debug: false,
    success: 'valid'
});


var form = $('#form1');
form.validate({
    rules: {
        file: {
            required: true
        }
    }
});

$.post('script/getUID.php', {getUID: "yes"}, function(id) {
	if (typeof id != 'undefined') {
		$('#uid').val(id);
	} else {
		console.log('Unable to get ID for this process!');
	}
});

$('#file').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
    }
});
