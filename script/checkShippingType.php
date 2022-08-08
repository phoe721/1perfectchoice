<?
require_once("class/costs.php");
require_once("class/packages.php");
require_once("class/shipping.php");
require_once("class/status.php");
require_once("class/validator.php");
$costs = new costs();
$packages = new packages();
$shipping = new shipping();
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
					$sku = clean_up($sku);
					list($code, $item_no) = explode("-", $sku, 2);
					$cost = $costs->get_cost($code, $item_no);
					list($length, $width, $height) = $packages->get_dimensions($code, $item_no);
					$weight = array_sum($packages->get_weight($code, $item_no));
					$ups_cost = $shipping->getUPSCost($cost, $length, $width, $height, $weight);
					if ($ups_cost == -1) {
						$result = "$sku\tLTL" . PHP_EOL;
					} else {
						$result = "$sku\tUPS" . PHP_EOL;
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
	$sku = clean_up($sku);
	list($code, $item_no) = explode("-", $sku, 2);
	$cost = $costs->get_cost($code, $item_no);
	list($length, $width, $height) = $packages->get_dimensions($code, $item_no);
	$weight = array_sum($packages->get_weight($code, $item_no));
	$ups_cost = $shipping->getUPSCost($cost, $length, $width, $height, $weight);
	if ($ups_cost == -1) {
		$result = "$sku\tLTL";
	} else {
		$result = "$sku\tUPS";
	}

	echo $result . PHP_EOL;
}
?>
