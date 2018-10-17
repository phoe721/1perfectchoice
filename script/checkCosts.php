<?
require_once("class/costs.php");
$costs = new costs();
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
				if (preg_match('/^[A-Z]+-[A-Z0-9-x]+$/', $sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					$cost = $costs->get_cost($code, $item_no);
					$unit = $costs->get_unit($code, $item_no);
					$result = "$sku\t$cost\t$unit" . PHP_EOL;
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
	$cost = $costs->get_cost($code, $item_no);
	$unit = $costs->get_unit($code, $item_no);
	$total = $cost * $unit;
	$result = "$sku has cost $cost and $unit per box and total is $total!";

	echo json_encode($result);
}
?>
