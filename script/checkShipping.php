<?
require_once("class/costs.php");
require_once("class/dimensions.php");
require_once("class/shipping.php");
$c = new costs();
$d = new dimensions();
$s = new shipping();
$status = new debugger();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_status_file($statusFile);
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if (preg_match('/^[A-Z]+-[A-Z0-9-]+$/', $sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					$cost = $c->get_cost($code, $item_no);
					list($length, $width, $height) = $d->get_dimensions($code, $item_no);
					$weight = $d->get_weight($code, $item_no);
					$ups_cost = $s->getUPSCost($cost, $length, $width, $height, $weight);
					$trucking_cost = $s->getTruckingCost($weight);
					$result = "$sku\t$ups_cost\t$trucking_cost" . PHP_EOL;
				} else {
					$status->info("Invalid SKU: $sku");
					$result = "$sku\t-\t-" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$cost = $c->get_cost($code, $item_no);
	list($length, $width, $height) = $d->get_dimensions($code, $item_no);
	$weight = $d->get_weight($code, $item_no);
	$ups_cost = $s->getUPSCost($cost, $length, $width, $height, $weight);
	$trucking_cost = $s->getTruckingCost($weight);
}
?>
