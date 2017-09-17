$.post('script/getUID.php', {getUID: "yes"}, function(id) {
	if (typeof id != 'undefined') {
		uid = id;
	} else {
		console.log('Unable to get ID for this process!');
	}
});

$.post('script/index.php', {getPageDirectory: "yes"}, function(pages) {
	var result = $.parseJSON(pages);
	$.each(result, function() {
		$('#menu').append("<option value='" + this.name + "'>" + this.name + "</option>");
	});
});

$('#menu').change(function() {
	var page = $('#menu').val();
	$.post('script/index.php', {getPage: page}, function(content) {
		var result = $.parseJSON(content);
		$.each(result, function() {
			$('#page').html(this.content);
		});
	});
	
});
