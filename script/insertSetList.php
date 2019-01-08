<?
require_once("class/set_list.php");
require_once("class/status.php");
require_once("class/validator.php");
$set_list = new set_list();
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
				list($sku, $item1, $item2, $item3, $item4, $item5, $item6, $item7, $item8, $item9, $item10) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if (empty($item2) && ($item_no == $item1)) { 
						$result = "$sku\tInvalid Set" . PHP_EO;
					} else {
						if ($set_list->insert($code, $item_no, $item1, $item2, $item3, $item4, $item5, $item6, $item7, $item8, $item9, $item10)) { 
							$result = "$sku\tOK" . PHP_EOL;
						} else {
							$result = "$sku\tFail" . PHP_EOL;
						}
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
