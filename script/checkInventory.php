<?
require_once("class/inventory.php");
require_once("class/status.php");
require_once("class/validator.php");
$inventory = new inventory();
$status = new status();
$validator = new validator();
$lines = $count = $inStock = 0;

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$lines = count(file($inputFile));
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if ($validator->check_sku($sku)) {
					$cleaned_sku = clean_up($sku);
					list($code, $item_no) = explode("-", $cleaned_sku, 2);
					$qty = $inventory->get($code, $item_no);
					if ($qty == -1) {
						// Do Nothing
					} else {
						$qty = ($qty >= MIN_INVENTORY_QUANTITY) ? $qty : 0;
						$qty = min(MAX_INVENTORY_QUANTITY, $qty);
						$qty = floor($qty / QUANTITY_DIVIDER);
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
	}

	$inStock = round(($count / $lines) * 100, 2);
	fwrite($output, "There are total $lines SKU(s) and $count with quantity. The percentage is $inStock%.\n");
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
