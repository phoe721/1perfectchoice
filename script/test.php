<?	
/* Initialization */
require_once("functions.php");

/*
$sku = "AC-00114-15";
$pieces = explode("-", $sku);
$vendor_code = $pieces[0];
for ($i = 1; $i < count($pieces); $i++) {
	$item_no[$i] = $pieces[$i];
}
echo $vendor_code . PHP_EOL;
var_dump($item_no);

if (check_vendor_code($vendor_code)) {
	$result = $db->query("SELECT cost FROM costs WHERE vendor_code = '" . $vendor_code . "' AND item_no = '" . $item_no . "'"); 
	if ($result) {
		while ($row = mysqli_fetch_array($result)) {
			$cost = $row['cost'];
			echo "Found item $sku: $cost" . PHP_EOL;
		}
	} else {
		echo "Cost not found!";
	}
}

function check_vendor_code($vendor_code) {
	global $db;
	$result = $db->query("SELECT COUNT(*) FROM vendors WHERE code = '" . $vendor_code . "'");
	if ($result && mysqli_num_rows($result) > 0) {
		return true;
	}

	return false;	
}

function get_set_list($vendor_code, $sku) {
	global $db;
	$item_no = array();
	$result = $db->query("SELECT sku1, sku2, sku3, sku4, sku5, sku6, sku7, sku8, sku9, sku10 FROM set_list WHERE vendor_code = '" . $vendor_code . "' AND sku = '" . $sku . "'");
	if ($result) { 
		while ($row = mysqli_fetch_array($result)) {
			for ($i = 1; $i <= 10; $i++) {
				$current_sku = $row['sku' . $i];
				if (!empty($current_sku)) {
					$item_no[$i] = $current_sku;
				}
			}
			return $item_no;
		}
	} else {
		echo "Set list not found!";
	}
}
 */
?>
