jQuery.validator.setDefaults({
    debug: false,
    success: 'valid'
});


var form = $('#form1');
form.validate({
    rules: {
        file: {
            required: true,
            accept: 'text/plain',
            extension: 'txt|csv'
        }
    }
});

$('#file').change(function() {
    if (!form.valid()) {
        console.log('Invalid File!');
    }
});
