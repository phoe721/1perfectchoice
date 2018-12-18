<?
require_once("class/packages.php");
require_once("class/status.php");
require_once("class/validator.php");
$pg = new packages();
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
				list($sku, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($pg->check_exist($code, $item_no)) {
						if ($pg->update($code, $item_no, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight)) {
							$result = "$sku\tOK" . PHP_EOL;
						} else {
							$result = "$sku\tFail" . PHP_EOL;
						}
					} else {
						$result = "$sku\tNot Exist" . PHP_EOL;
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
