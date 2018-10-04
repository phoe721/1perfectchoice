<?
require_once("class/discontinued.php");
$dis = new discontinued();
$status = new debugger();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_status_file($statusFile);
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$sku = trim(fgets($input));
			if (!empty($sku)) {
				$status->log_status("Checking $sku...");
				if (preg_match('/^[A-Z]+-[A-Z0-9]+$/', $sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($dis->check($code, $item_no)) { 
						$result = "$sku\tDiscontinued" . PHP_EOL;
					} else {
						$result = "$sku\tActive" . PHP_EOL;
					}
				} else {
					$status->info("Invalid SKU: $sku");
					$result = "$sku\t-" . PHP_EOL;
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
	$dis->check($code, $item_no);
}
?>
