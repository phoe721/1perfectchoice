<?
require_once("class/inventory.php");
require_once("class/status.php");
require_once("class/validator.php");
require_once("class/debugger.php");
$inventory = new inventory();
$status = new status();
$validator = new validator();
$output = new debugger();
$output->set_console(true);
$output->set_log_level(4);

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$output = fopen($outputFile, "a+");
	if ($output) {
		$status->log_status("Checking each vendor inventory percentage...");
		$vendors = $inventory->get_vendors();
		foreach($vendors as $vendor) {
			$inStock = $inventory->check_vendor_in_stock_count($vendor);
			$itemCount = $inventory->check_vendor_item_count($vendor);
			$inStockPercentage = 100 * round($inStock / $itemCount, 2);
			$result = "$vendor has total $itemCount items and $inStock items are in stock. In stock percentage is $inStockPercentage%" . PHP_EOL;
			fwrite($output, $result);
		}
	}

	$status->log_status("Done!");
	fclose($output);
} else {
	$vendors = $inventory->get_vendors();
	$result = "";
	foreach($vendors as $vendor) {
		$inStock = $inventory->check_vendor_in_stock_count($vendor);
		$itemCount = $inventory->check_vendor_item_count($vendor);
		$inStockPercentage = 100 * round($inStock / $itemCount, 2);
		$result .= "$vendor has total $itemCount items and $inStock items are in stock. In stock percentage is $inStockPercentage%" . PHP_EOL;
		$output->info($result);
	}
	mail(ADMIN_GROUP_MAIL,"Inventory Percentage",$result);
}
?>
