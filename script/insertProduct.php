<?
require_once("class/product.php");
require_once("class/status.php");
require_once("class/validator.php");
$product = new product();
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
				list($sku, $item_type, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $feature6, $feature7, $feature8, $feature9, $feature10, $color, $material) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($product->check_exist($code, $item_no)) {
						$result = "$sku\tExists" . PHP_EOL;
					} else {
						if ($product->insert($code, $item_no, $item_type, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $feature6, $feature7, $feature8, $feature9, $feature10, $color, $material)) {
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
