<?	
/* Initialization */
require_once("functions.php");
require_once("getCategory.php");

$str = "Simple Relax 1PerfectChoice Modern 2 Pcs Bar Pub Stool Barstool Adjustable Height Black PU Seat Chrome Base";
$result = get_type($str);
echo "Result: $result" . PHP_EOL;
/*
$sku = "AC-00450-453";
$vendor_code = get_vendor_code($sku); 
$item_array = get_set_list($sku);

if (empty($item_array)) $item_array = get_item_no($sku);	
var_dump($item_array);

$total_cost = 0;
if (check_vendor_code($vendor_code)) {
	for ($i = 0; $i < count($item_array); $i++) {
		$cost = get_cost($vendor_code, $item_array[$i]);
		$total_cost += $cost;
	}
	echo "Total Cost: " . $total_cost . PHP_EOL;
} else {
	echo "Invalid Vendor Code " . PHP_EOL;
}

function check_vendor_code($vendor_code) {
	global $db;
	$result = $db->query("SELECT COUNT(*) FROM vendors WHERE code = '" . $vendor_code . "'");
	if ($result && mysqli_num_rows($result) > 0) return true;

	return false;	
}

function get_vendor_code($sku) {
	if (!empty($sku)) {
		$pieces = explode("-", $sku);
		return $pieces[0];
	} else {
		echo "Invalid SKU: $sku!" . PHP_EOL;
	}
}

function get_item_no($sku) {
	if (!empty($sku)) {
		$vendor_code = get_vendor_code($sku);
		$item_str = str_replace($vendor_code . "-", "", $sku);
		$item_array = explode("-", $item_str); 
		$first_len = strlen($item_array[0]);
		for ($i = 1; $i < count($item_array); $i++) {
			$current_len = strlen($item_array[$i]);
			if ($first_len > $current_len) {
				$diff = $first_len - $current_len;
				$item_array[$i] = substr($item_array[0], 0, $diff) . $item_array[$i];
			}
		}

		return $item_array;
	} else {
		echo "Invalid SKU: $sku!" . PHP_EOL;
	}
}

function get_set_list($sku) {
	global $db;
	$vendor_code = get_vendor_code($sku);
	$item_str = str_replace($vendor_code . "-", "", $sku);

	$item_array = array();
	$result = $db->query("SELECT sku1, sku2, sku3, sku4, sku5, sku6, sku7, sku8, sku9, sku10 FROM set_list WHERE vendor_code = '" . $vendor_code . "' AND sku = '" . $item_str . "'");
	if ($result) { 
		while ($row = mysqli_fetch_array($result)) {
			for ($i = 1; $i <= 10; $i++) {
				$current_sku = $row['sku' . $i];
				if (!empty($current_sku)) {
					$item_array[$i-1] = $current_sku;
				}
			}
		}
	}

	return $item_array;
}

function get_cost($vendor_code, $item_no) {
	global $db;
	$result = $db->query("SELECT cost FROM costs WHERE vendor_code = '" . $vendor_code . "' AND item_no = '" . $item_no . "'"); 
	if ($result) {
		while ($row = mysqli_fetch_array($result)) {
			$cost = $row['cost'];
			echo "Found item " . $item_no . ": $cost" . PHP_EOL;

			return $cost;
		}
	} else {
		echo "Cost not found!" . PHP_EOL;
	}
}
 */
?>
