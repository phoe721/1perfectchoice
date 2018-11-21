<?
require_once("class/ASIN.php");
require_once("class/status.php");
require_once("class/validator.php");
$a = new ASIN();
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
			$asin = trim(fgets($input));
			$status->log_status("Checking $asin...");
			if (!empty($asin)) {
				if ($validator->check_asin($asin)) {
					$sku = $a->get_sku($asin);
					$result = "$asin\t$sku" . PHP_EOL;
				} else {
					$result = "$asin\tInvalid" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["asin"])) { 
	$asin = $_POST["asin"];
	$sku = $a->get_sku($asin);
	$result = "$asin has SKU $sku!";

	echo json_encode($result);
}
?>
