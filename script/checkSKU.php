<?
require_once("class/ASIN.php");
require_once("class/UPC.php");
require_once("class/status.php");
require_once("class/validator.php");
$ASIN = new ASIN();
$UPC = new UPC();
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
			$status->log_status("Checking $line...");
			if (!empty($line)) {
				if ($validator->check_asin($line)) {
					$sku = $ASIN->get_sku($line);
					$result = "$line\t$sku" . PHP_EOL;
				} else if ($validator->check_upc($line)) {
					$sku = $UPC->get_sku($line);
					$result = "$line\t$sku" . PHP_EOL;
				} else {
					$result = "$asin\tInvalid" . PHP_EOL;
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
