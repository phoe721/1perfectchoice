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

$('#file').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
    }
});
