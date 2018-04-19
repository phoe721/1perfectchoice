<?	
/* Initialization */
require_once("functions.php");

$sku = "AC-00450-452";
$pieces = explode("-", $sku);
$vendor_code = $pieces[0];
array_shift($pieces);
$sku = implode("-", $pieces);
$item_no = get_set_list($vendor_code, $sku);
$total_cost = 0;

if (check_vendor_code($vendor_code)) {
	global $db;
	for ($i = 1; $i <= count($item_no); $i++) {
		$result = $db->query("SELECT cost FROM costs WHERE vendor_code = '" . $vendor_code . "' AND item_no = '" . $item_no[$i] . "'"); 
		if ($result) {
			while ($row = mysqli_fetch_array($result)) {
				$cost = $row['cost'];
				$total_cost += $cost;
				echo "Found item " . $item_no[$i] . ": $cost" . PHP_EOL;
			}
		} else {
			echo "Cost not found!";
		}
	}
}
echo $total_cost;

function check_vendor_code($vendor_code) {
	global $db;
	$result = $db->query("SELECT COUNT(*) FROM vendors WHERE code = '" . $vendor_code . "'");
	if ($result && mysqli_num_rows($result) > 0) return true;

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
		}
	}

	return $item_no;
}
?>
