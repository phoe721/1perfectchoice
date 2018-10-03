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
			$line = trim(fgets($input));
			if (!empty($line)) {
				$status->log_status("Inserting $line...");
				list($sku, $new_cost, $unit) = explode("\t", $line);
				list($code, $item_no) = explode("-", $sku, 2);
				if ($costs->insert($code, $item_no, $new_cost, $unit)) { 
					$result = "$sku\tOK" . PHP_EOL;
				} else {
					$result = "$sku\tFail" . PHP_EOL;
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
