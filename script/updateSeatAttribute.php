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
			$line = trim(fgets($input));
			if (!empty($line)) {
				$status->log_status("Updating $line...");
				list($sku, $item_type, $seat_width, $seat_depth, $seat_height, $back_height, $seat_back_width, $seat_back_height, $seat_bottom_depth, $seat_bottom_width, $seat_bottom_thickness, $maximum_seat_height, $minimum_seat_height) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($seat_attribute->check_exist($code, $item_no)) {
						if ($seat_attribute->update($code, $item_no, $seat_width, $seat_depth, $seat_height, $back_height, $seat_back_width, $seat_back_height, $seat_bottom_depth, $seat_bottom_width, $seat_bottom_thickness, $maximum_seat_height, $minimum_seat_height)) {
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
