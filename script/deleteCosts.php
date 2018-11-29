<?
require_once("class/costs.php");
require_once("class/status.php");
require_once("class/validator.php");
$costs = new costs();
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
				$status->log_status("Deleteing $sku...");
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($costs->check_exist($code, $item_no)) {
						$result = $costs->delete($code, $item_no); 
						$result = $result ? "$sku\tOK" . PHP_EOL : "$sku\tFail" . PHP_EOL;
					} else {
						$result = "$sku\tNot Exist" . PHP_EOL;
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
