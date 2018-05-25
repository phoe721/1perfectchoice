<?	
/* Initialization */
require_once("functions.php");
require_once("getCosts.php");

if (isset($argv[1])) {
	$skus = $argv[1];
	$vendor_code = get_vendor_code($skus);
	echo "Vendor code: $vendor_code" . PHP_EOL;
	$item_array = get_set_list($skus);
	if (empty($item_array)) $item_array = get_item_no($skus);
	
	$total_cost = 0;
	if (check_vendor_code($vendor_code)) {
		for ($i = 0; $i < count($item_array); $i++) {
			$cost = get_cost($vendor_code, $item_array[$i]);
			$total_cost += $cost;
			echo "Item No.: " . $item_array[$i] . ", Cost: " . $cost . PHP_EOL;
		}
		echo "Total Cost: " . $total_cost . PHP_EOL;
	} else {
		echo "Invalid Vendor Code!" . PHP_EOL;
	}
}

?>
