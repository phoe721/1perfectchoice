<?
require_once("class/seat_attribute.php");
require_once("class/status.php");
require_once("class/validator.php");
$seat_attribute = new seat_attribute();
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
					list($code, $item_no) = explode("-", $sku, 2);
					$seat_width = $seat_attribute->get_seat_width($code, $item_no);
					$seat_depth = $seat_attribute->get_seat_depth($code, $item_no);
					$seat_height = $seat_attribute->get_seat_height($code, $item_no);
					$result = "$sku\t$seat_width\t$seat_depth\t$seat_height" . PHP_EOL;
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
} if (isset($argv[1]) && isset($argv[2])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					$seat_width = $seat_attribute->get_seat_width($code, $item_no);
					$seat_depth = $seat_attribute->get_seat_depth($code, $item_no);
					$seat_height = $seat_attribute->get_seat_height($code, $item_no);
					$result = "$sku\t$seat_width\t$seat_depth\t$seat_height" . PHP_EOL;
				} else {
					$result = "$sku\tInvalid" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}

	fclose($input);
	fclose($output);
}
?>
