<?
require_once("class/vendors.php");
require_once("class/inventory.php");
$vendor = new vendors();
$inventory = new inventory();
$output = new debugger();
$output->set_console(true);
$set_mail = false;

$result = "";
$vendor_codes = $inventory->get_vendors();
if (!empty($vendor_codes)) {
	foreach($vendor_codes as $vendor_code) {
		$vendor_name = $vendor->get_name($vendor_code);
		$inStock = $inventory->check_vendor_in_stock_count($vendor_code);
		$itemCount = $inventory->check_vendor_item_count($vendor_code);
		$inStockPercentage = 100 * round($inStock / $itemCount, 2);
		$result .= "$vendor_name: $inStock / $itemCount. Stock percentage:  $inStockPercentage%" . PHP_EOL;
	}
} else {
	$result = "Inventory is empty!";
}
$output->console($result);

if ($set_mail) mail(ADMIN_GROUP_MAIL, "Inventory Percentage", $result);
?>
