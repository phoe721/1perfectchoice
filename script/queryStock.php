<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if (isset($_POST['sku']) && isset($_POST['uid'])) {
	$sku = $_POST['sku'];
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	log_status("Looking up $sku...");
	$mesg = '';
	$sku = trim($sku);
	$newSKU = filter_sku_vendor_code($sku);
	$qty = query_stock($newSKU);
	if ($qty == -1) {
		$skus = check_set_sku($newSKU);
		if (is_array($skus)) {
			foreach ($skus as $newSKU) {
				$qty = query_stock($newSKU);
				if ($qty == -1) {
					$mesg .= "Cannot find SKU $newSKU in stock!<br>";
				} else {
					$mesg .= "SKU $newSKU has quantity $qty!<br>";
				}
			}
		} else {
			$mesg = "Cannot find SKU $sku in stock!";
		}
	} else {
		$mesg = "SKU $sku has quantity $qty!";
	}

	$result['status'] = $mesg;
	echo json_encode($result);
}
?>
