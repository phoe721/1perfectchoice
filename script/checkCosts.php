<?
require_once("class/costs.php");
$costs = new costs();
$statusFile = "";

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$input = fopen($inputFile, "r");
	$result = fopen($outputFile, "a+");
	if ($input && $result) {
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				log_status("Checking $sku...");
				list($code, $item_no) = explode("-", $sku, 2);
				$output = "$code-$item_no: " . $costs->get_cost($code, $item_no) . PHP_EOL;
				fwrite($result, $output);
			}
		}
	}

	fclose($input);
	fclose($result);
	log_status("Done");
}

function log_status($msg) {
	global $statusFile;
	$status = fopen($statusFile, "w");
	$msg = $msg . PHP_EOL;
	if ($status) fwrite($status, $msg);
	fclose($status);
	
}
?>
