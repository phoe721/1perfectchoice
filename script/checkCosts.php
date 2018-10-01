<?
require_once("class/costs.php");
$costs = new costs();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$uid = $argv[1];
	$input = $argv[2];
	$output = $argv[3];
	$file = fopen($input, "r");
	$file2 = fopen($output, "a+");
	while(!feof($file)) {
		$line = trim(fgets($file));
		if (!empty($line)) {
			list($code, $item_no) = explode("-", $line, 2);
			$output = $costs->get_cost($code, $item_no);
			fwrite($file2, $output);
		}
	}
	fclose($file);
	fclose($file2);
}
?>
