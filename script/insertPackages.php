<?
require_once("class/packages.php");
$pg = new packages();
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
				list($sku, $length, $width, $height, $weight) = explode("\t", $line);
				if (preg_match('/^[A-Z]+-[A-Z0-9-x. ]+$/', $sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($pg->insert($code, $item_no, $length, $width, $height, $weight)) {
						$result = "$sku\tOK" . PHP_EOL;
					} else {
						$result = "$sku\tFail" . PHP_EOL;
					}
				} else {
					$status->info("Invalid SKU: $sku");
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
