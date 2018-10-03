<?
require_once("class/discontinued.php");
$dis = new discontinued();
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
				list($code, $item_no) = explode("-", $sku, 2);
				$result = $dis->check($code, $item_no) ? "$sku\tDiscontinued" . PHP_EOL : "$sku\tActive" . PHP_EOL;
				fwrite($output, $result);
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}
?>
