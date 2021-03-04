<?
require_once("class/costs.php");
require_once("class/set_list.php");
require_once("class/phpequations.class.php");
require_once("class/status.php");
require_once("class/validator.php");
$costs = new costs();
$set_list = new set_list();
$status = new status();
$validator = new validator();
$pair = array();
$sum = 0;

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
				$status->log_status("Processing $line...");
				list($sku, $goal) = explode("\t", $line);;
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($set_list->check($code, $item_no)) {
						$set = $set_list->get_set($code, $item_no);
						foreach ($set as $part) {
							$part_cost = $costs->get_cost($code, $part);
							$pair[$part] = $part_cost;
							$sum += $part_cost;
							$result .= "$part\t$part_cost" . PHP_EOL;
						}
					} else {
						$result = "$sku\tNo A Set" . PHP_EOL;
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
