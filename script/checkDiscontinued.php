<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if (isset($_POST["sku"])) {
	$sku = $_POST["sku"];

	// Check SKU in DB
	if (checkDiscontinued($sku)) {
		$output = "SKU: $sku, Status: Discontinued";
	} else {
		$output = "SKU: $sku, Status: Active";
	}
	$result['status'] = $output;
	echo json_encode($result);
}

function checkDiscontinued($sku) {
	global $db;
	$db_result = $db->query("SELECT * FROM product_discontinued WHERE sku = '$sku'");
	if (mysqli_num_rows($db_result) > 0) {
		return true;
	} else {
		return false;
	}
}
?>
