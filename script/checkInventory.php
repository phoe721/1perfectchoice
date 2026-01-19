<?
require_once("class/inventory.php");
require_once("class/discontinued.php");
require_once("class/status.php");
require_once("class/validator.php");
require_once("class/stop_watch.php");
$inventory = new inventory();
$dis = new discontinued();
$status = new status();
$validator = new validator();
$sw = new stop_watch();
$lines = $count = $inStock = 0;
$min_inventory_quantity = 5;
$max_inventory_quantity = 100;
$quantity_divider = 5;

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$lines = count(file($inputFile));
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		$sw->start();
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if ($validator->check_sku($sku)) {
					$cleaned_sku = clean_up($sku);
					list($code, $item_no) = explode("-", $cleaned_sku, 2);
					$qty = $inventory->get($code, $item_no);
					if ($qty == -1) {
						if ($dis->check($code, $item_no)) { 
							$qty = -2;
						} else {
							// Inventory Not Found, Do Nothing
						}
					} else {
						$qty = ($qty >= $min_inventory_quantity) ? $qty : 0;
						$qty = min($max_inventory_quantity, $qty);
						$qty = floor($qty / $quantity_divider);
						if ($qty > 0) $count++;
					}
					$updated_time = $inventory->get_updated_time($code, $item_no);
					$result = "$sku\t$qty\t$updated_time" . PHP_EOL;
				} else {
					$result = "$sku\tInvalid" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
		$sw->stop();
	}

	$inStock = round(($count / $lines) * 100, 2);
	fwrite($output, "There are total $lines SKU(s) and $count with quantity. The percentage is $inStock%." . PHP_EOL);
	$status->log_status("Done!");
	fclose($input);
	fclose($output);
} else if (isset($argv[1])) {
	$sku = $argv[1];
	if ($validator->check_sku($sku)) {
		list($code, $item_no) = explode("-", $sku, 2);
		$qty = $inventory->get($code, $item_no);

		printf("The quantity for $sku is $qty!\n");
	} else {
		printf("$sku is invalid SKU!\n");
	}
}
?>
