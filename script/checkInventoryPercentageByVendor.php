<?
require_once("class/inventory.php");
$inventory = new inventory();
$output = new debugger();
$output->set_console(true);
$set_mail = false;

$result = "";
$vendors = $inventory->get_vendors();
foreach($vendors as $vendor) {
	$inStock = $inventory->check_vendor_in_stock_count($vendor);
	$itemCount = $inventory->check_vendor_item_count($vendor);
	$inStockPercentage = 100 * round($inStock / $itemCount, 2);
	$result .= "$vendor has total $itemCount items and $inStock items are in stock. In stock percentage is $inStockPercentage%" . PHP_EOL;
}
$output->info($result);

if ($set_mail) mail(ADMIN_GROUP_MAIL,"Inventory Percentage",$result);
?>
