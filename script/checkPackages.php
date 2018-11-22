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
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					$box_count = $pg->get_box_count($code, $item_no);
					$weights = $pg->get_weight($code, $item_no);
					$dimensions = $pg->get_dimensions($code, $item_no);
					$result = "$sku\t";
					for ($i = 0; $i < $box_count; $i++) {
						$result .= $dimensions[$i] . "\t" . $dimensions[$i+1] . "\t" . $dimensions[$i+2] . "\t" . $weights[$i];
					}
					$result .= PHP_EOL;
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

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$box_count = $pg->get_box_count($code, $item_no);
	$weights = $pg->get_weight($code, $item_no);
	$dimensions = $pg->get_dimensions($code, $item_no);
	$result = "$sku has ";
	for ($i = 0; $i < $box_count; $i++) {
		$count = $i + 1;
		$result .= "box $count weight: $weights[0] lbs, ";
		$result .= "box $count dimensions: " . $dimensions[$i] . " x " . $dimensions[$i+1] . " x " . $dimensions[$i+2] . "." . PHP_EOL;
	}

	echo json_encode($result);
}
?>
