<?
require_once("class/UPC.php");
require_once("class/status.php");
require_once("class/validator.php");
$u = new UPC();
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
				list($sku, $upc) = explode("\t", $line);
				if ($validator->check_upc($upc)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($u->check_exist($code, $item_no)) {
						$result = $u->update($code, $item_no, $upc); 
						$result = $result ? "$sku\tOK" . PHP_EOL : "$sku\tFail" . PHP_EOL;
					} else {
						$result = "$sku\tNot Exist" . PHP_EOL;
					}
				} else {
					$result = "$sku\tInvalid UPC" . PHP_EOL;
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
