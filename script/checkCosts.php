<?
require_once("class/vendors.php");
require_once("class/costs.php");
$vendor = new vendors();
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
				list($code, $item_no) = $vendor->separate($sku);
				if ($vendor->check($code)) {
					$cost = $costs->get_cost($code, $item_no);
					$unit = $costs->get_unit($code, $item_no);
					$result = "$sku\t$cost\t$unit" . PHP_EOL;
					fwrite($output, $result);
				}
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = $vendor->separate($sku);
	if ($vendor->check($code)) {
		list($code, $item_no) = explode("-", $sku, 2);
		$costs->get_cost($code, $item_no);
		$costs->get_unit($code, $item_no);
	}

}
?>
