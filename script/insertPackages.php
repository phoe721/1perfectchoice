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
				$status->log_status("Inserting $line...");
				list($sku, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight, $box6_length, $box6_width, $box6_height, $box6_weight, $box7_length, $box7_width, $box7_height, $box7_weight, $box8_length, $box8_width, $box8_height, $box8_weight, $box9_length, $box9_width, $box9_height, $box9_weight, $box10_length, $box10_width, $box10_height, $box10_weight, $box11_length, $box11_width, $box11_height, $box11_weight) = explode("\t", $line);
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($pg->check_exist($code, $item_no)) {
							$result = "$sku\tExists" . PHP_EOL;
					} else {
						if ($pg->insert($code, $item_no, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight, $box6_length, $box6_width, $box6_height, $box6_weight, $box7_length, $box7_width, $box7_height, $box7_weight, $box8_length, $box8_width, $box8_height, $box8_weight, $box9_length, $box9_width, $box9_height, $box9_weight, $box10_length, $box10_width, $box10_height, $box10_weight, $box11_length, $box11_width, $box11_height, $box11_weight)) {
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
