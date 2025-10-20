$(document).ready(function() {
	// Hide Output 
	$('#box1').hide();

	// Reset
	$('#reset').click(function() {
		location.reload();
	});

	// Check inventory Percentage
	$('#check_discontinued_total').click(function() {
		$.post('script/checkDiscontinuedTotal.php', function(data) {
			alert(data);
		});
	});
});
