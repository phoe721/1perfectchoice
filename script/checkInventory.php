<?
require_once("class/inventory.php");
require_once("class/status.php");
require_once("class/validator.php");
$inventory = new inventory();
$status = new status();
$validator = new validator();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					$qty = $inventory->get($code, $item_no);
					$qty = ($qty >= MIN_INVENTORY_QUANTITY) ? $qty : 0;
					$qty = min(MAX_INVENTORY_QUANTITY, $qty);
					$qty = floor($qty / QUANTITY_DIVIDER);
					$result = "$sku\t$qty" . PHP_EOL;
				} else {
					$result = "$sku\tInvalid" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}

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
