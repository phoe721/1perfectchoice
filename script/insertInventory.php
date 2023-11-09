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
			$line = trim(fgets($input));
			if (!empty($line)) {
				$status->log_status("Inserting $line...");
				list($sku, $qty) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($inventory->check_exist($code, $item_no)) {
						$result = "$sku\tExists" . PHP_EOL;
					} else {
						if (!empty($qty) && $qty > 0) $count++;
						$result = $inventory->insert($code, $item_no, $qty);
						$result = $result ? "$sku\tOK" . PHP_EOL : "$sku\tFail" . PHP_EOL;
					}
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
}
?>
