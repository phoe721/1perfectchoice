<?
require_once("class/product_type.php");
require_once("class/status.php");
require_once("class/validator.php");
$product_type = new product_type();
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
			$line = trim(fgets($input));
			if (!empty($line)) {
				$status->log_status("Inserting $line...");
				//list($sku, $item_type) = explode("\t", $line);
				list($sku, $item_type) = array_pad(explode("\t", $line ?? ''), 2, null);
				if ($validator->check_sku($sku)) {
					//list($code, $item_no) = explode("-", $sku, 2);
					list($code, $item_no) = array_pad(explode("-", $sku ?? ''), 2, null);
					if ($product_type->check_exist($code, $item_no)) {
						$result = "$sku\tExists" . PHP_EOL;
					} else {
						if ($product_type->insert($code, $item_no, $item_type)) {
							$result = "$sku\tOK" . PHP_EOL;
						} else {
							$result = "$sku\tFail" . PHP_EOL;
						}
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
}
?>
