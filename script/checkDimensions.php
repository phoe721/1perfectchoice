<?
require_once("class/dimensions.php");
require_once("class/set_list.php");
require_once("class/status.php");
require_once("class/validator.php");
$dim = new dimensions();
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
					list($code, $item_no) = explode("-", $sku, 2);
					$dimensions = implode("\t", $dim->get_dimensions($code, $item_no));
					$result = "$sku\t$dimensions" . PHP_EOL;
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
	$dimensions = $dim->get_dimensions($code, $item_no);
	if ($set_list->check($code, $item_no)) {
		$set = $set_list->get_set($code, $item_no);
		$result = "$sku has ";
		for ($i = 0; $i < count($set); $i++) {
			$item = $set[$i];
			$result .= "$item dimensions: " . $dimensions[$i*3] . " x " . $dimensions[$i*3+1] . " x " . $dimensions[$i*3+2] . ". ";
		}
	} else {
		$result = "$sku has dimensions " . implode(" x ", $dimensions) . "!";
	}

	echo json_encode($result);
}
?>
