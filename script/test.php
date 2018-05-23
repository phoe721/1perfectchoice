<?	
/* Initialization */
require_once("functions.php");
require_once("getCosts.php");

$skus = "AC-00450-452";
$vendor_code = get_vendor_code($skus);
echo $vendor_code;
$item_array = get_set_list($skus);
var_dump($item_array);

$total_cost = 0;
if (check_vendor_code($vendor_code)) {
	for ($i = 0; $i < count($item_array); $i++) {
		$cost = get_cost($vendor_code, $item_array[$i]);
		$total_cost += $cost;
	}
	echo "Total Cost: " . $total_cost;
} else {
	echo "Invalid Vendor Code!";
}

?>
