jQuery.validator.setDefaults({
    debug: false,
    success: "valid"
});

var form = $('#form');
form.validate({
    rules: {
		start: {
            required: true
		},
		step: {
        	required: true
		},
		max: {
        	required: true
		},	
        url: {
            required: true,
            url: true
        }
    }
});

var uid = '';
$.post('script/getUID.php', {getUID: "yes"}, function(id) {
	if (typeof id != 'undefined') {
		uid = id;
	} else {
		console.log('Unable to get ID for this process!');
	}
});

$('#url').bind('paste', function(e) {
	var pastedData = e.originalEvent.clipboardData.getData('text');
	this.style.width = ((pastedData.length + 1) * 6) + 'px';
});

$('#reset').click(function() {
	$('#url').val('').css('width', '150px');
	$('#url-error').html('').removeClass('error valid');
	$('#start').val('').css('width', '150px');
	$('#start-error').html('').removeClass('error valid');
	$('#step').val('').css('width', '150px');
	$('#step-error').html('').removeClass('error valid');
	$('#max').val('').css('width', '150px');
	$('#max-error').html('').removeClass('error valid');
	$('#output').html('');
	$('#submit').removeAttr('disabled');
});

var url, time = 5000;
$('#submit').click(function() {
    if (form.valid()) {
		$('#submit').attr('disabled','disabled');
        url = $('#url').val();
		start = $('#start').val();
		step = $('#step').val();
		max = $('#max').val();
        $.post('script/multipleListHouzz_v2.php', {url: url, start: start, step: step, max: max, uid: uid}, function(result) {
			$('#output').html(result.status + "<br>");
			$('#submit').removeAttr('disabled');
		}, "json");

		var prev = cur = '';
		var check = setInterval(function(){ 
			$.post('script/getStatus.php', {uid: uid}, function(result) { 
				cur = result.status;
				if (cur.match(/Done/)) {
					$('#output').html('');
					for (var i = 0; i < result.link.length; i++) {	
	    				$('#output').append('<a href="' + result.link[i] + '" target="_blank" download>' + result.link[i] + '</a><br>');
					}
					clearInterval(check);
				} else if (prev != cur) {
					$('#output').append(cur + "<br>");
					prev = cur;
				}
			}, "json");
		}, time);
    } else {
		$('#output').html('Invalid URL!');
    }
});

