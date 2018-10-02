<?
require_once("class/costs.php");
$costs = new costs();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$input = fopen($inputFile, "r");
	$result = fopen($outputFile, "a+");
	$status = fopen($statusFile, "w+");
	if ($input && $result && $status) {
		while(!feof($input)) {
			$line = trim(fgets($input));
			if (!empty($line)) {
				fwrite($status, "Checking $line..." . PHP_EOL);
				list($code, $item_no) = explode("-", $line, 2);
				$output = "$code-$item_no: " . $costs->get_cost($code, $item_no) . PHP_EOL;
				fwrite($result, $output);
			}
		}
	}

	fwrite($status, "Done" . PHP_EOL);
	fclose($input);
	fclose($result);
	fclose($status);
}
?>
