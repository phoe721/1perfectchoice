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
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if ($validator->check_sku($sku)) {
					$cleaned_sku = clean_up($sku);
					list($code, $item_no) = explode("-", $cleaned_sku, 2);
					if ($set_list->check($code, $item_no)) {
						$set_str = implode("\t", $set_list->get_set($code, $item_no));
						$result = "$sku\t$set_str" . PHP_EOL;
					} else {
						$result = "$sku\tNo" . PHP_EOL;
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
} else if (isset($argv[1])) {
	$sku = $argv[1];
	if ($validator->check_sku($sku)) {
		list($code, $item_no) = explode("-", $sku, 2);
		if ($set_list->check($code, $item_no)) {
			$set_str = implode(",", $set_list->get_set($code, $item_no));
			printf("$sku: $set_str\n");
		} else {
			printf("$sku is not a set list!\n");
		}
	} else {
		printf("$sku is invalid SKU!\n");
	}
}
?>
