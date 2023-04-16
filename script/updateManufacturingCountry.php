<?
require_once("class/manufacturing_country.php");
require_once("class/status.php");
require_once("class/validator.php");
$manufacturing_country = new manufacturing_country();
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
				$status->log_status("Updating $line...");
				list($sku, $country) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($manufacturing_country->check_exist($code, $item_no)) {
						if ($manufacturing_country->update($code, $country)) {
							$result = "$sku\tOK" . PHP_EOL;
						} else {
							$result = "$sku\tFail" . PHP_EOL;
						}
					} else {
						$result = "$sku\tNo Such Product" . PHP_EOL;
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
