<?
require_once("class/inventory_ETA.php");
require_once("class/helper_functions.php");
require_once("class/status.php");
require_once("class/validator.php");
$inventory_ETA = new inventory_ETA();
$status = new status();
$validator = new validator();
$ETA = "";

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
				$status->log_status("Checking $sku ETA...");
				if ($validator->check_sku($sku)) {
					$cleaned_sku = clean_up($sku);
					list($code, $item_no) = explode("-", $cleaned_sku, 2);
					$ETA = $inventory_ETA->check($code, $item_no);
					if (empty($ETA)) {
						$result = "$sku\tNot Found" . PHP_EOL;
					} else {
						$result = "$sku\t$ETA" . PHP_EOL;
					}
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
		$ETA = $inventory_ETA->check($code, $item_no);
		if (empty($ETA)) {
			printf("The ETA for $sku is not found!\n");
		} else {
			printf("The ETA for $sku is $ETA!\n");
		}
	} else {
		printf("$sku is invalid SKU!\n");
	}
}
?>
