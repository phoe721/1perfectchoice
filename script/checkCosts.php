<?
require_once("class/costs.php");
$costs = new costs();

if (isset($argv[1])) {
	$input = $argv[1];
	$file = fopen($input, "r");
	$file2 = fopen(DOWNLOAD . "result.txt", "a+");
	if ($file && $file2) {
		while(!feof($file)) {
			$line = trim(fgets($file));
			if (!empty($line)) {
				list($code, $item_no) = explode("-", $line, 2);
				$output = "$code-$item_no: " . $costs->get_cost($code, $item_no) . PHP_EOL;
				fwrite($file2, $output);
			}
		}
	}
	fclose($file);
	fclose($file2);
}
?>
