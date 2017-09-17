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

$(document).ready(function(){
	$('#sku').keypress(function(e){
		if (e.keyCode == 13) {
			e.preventDefault();
			$('#submit').click();
		}
	});
});

var uid = '';
$.post('script/getUID.php', {getUID: "yes"}, function(id) {
	if (typeof id != 'undefined') {
		uid = id;
	} else {
		console.log('Unable to get ID for this process!');
	}
});

$('#reset').click(function() {
	$('#sku-error').html('').removeClass('error valid');
	$('#output').html('');
	$('#submit').removeAttr('disabled');
});

var sku;
$('#submit').click(function() {
    if (form.valid()) {
		$('#submit').attr('disabled','disabled');
        sku = $('#sku').val();
        $.post('script/queryStock.php', {sku: sku, uid: uid}, function(result) {
			$('#output').html(result.status + "<br>");
			$('#submit').removeAttr('disabled');
		}, "json");
    } else {
		$('#output').html('Invalid SKU!');
    }
});

