<?
require_once("class/discontinued.php");
require_once("class/status.php");
require_once("class/validator.php");
$dis = new discontinued();
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
				$status->log_status("Inserting $sku...");
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($dis->check($code, $item_no)) {
						$result = "$sku\tExists" . PHP_EOL;
					} else {
						$result = $dis->insert($code, $item_no); 
						$result = $result ? "$sku\tOK" . PHP_EOL : "$sku\tFail" . PHP_EOL;
					}
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
