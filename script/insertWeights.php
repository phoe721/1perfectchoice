<?
require_once("class/weights.php");
require_once("class/status.php");
require_once("class/validator.php");
$weights = new weights();
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
				list($sku, $weight) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($weights->check_exist($code, $item_no)) {
						$result = "$sku\tExists" . PHP_EOL;
					} else {
						$result = $weights->insert($code, $item_no, $weight);
						$result = $result ? "$sku\tOK" . PHP_EOL : "$sku\tFail" . PHP_EOL;
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
