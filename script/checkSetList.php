<?
require_once("class/set_list.php");
require_once("class/status.php");
$set_list = new set_list();
$status = new status();

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
				if (preg_match('/^[A-Z]+-[A-Z0-9-x. ]+$/', $sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($set_list->check($code, $item_no)) {
						$result = "$sku\tYes" . PHP_EOL;
					} else {
						$result = "$sku\tNo" . PHP_EOL;
					}
				} else {
					$status->info("Invalid SKU: $sku");
					$result = "$sku\t-\t-" . PHP_EOL;
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
	if ($set_list->check($code, $item_no)) {
		$set_str = implode(", ", $set_list->get_set($code, $item_no));
		$result = "$sku has set $set_str!"; 
	} else {
		$result = "$sku is not a set!";
	}

	echo json_encode($result);
}
?>
