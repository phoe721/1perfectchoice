<?
require_once("class/costs.php");
$costs = new costs();
$file = fopen(UPLOAD . "input.txt", "r");
while(!feof($file)) {
	$line = trim(fgets($file));
	if (!empty($line)) {
		list($code, $item_no) = explode("-", $line, 2);
		echo $costs->get_cost($code, $item_no);
	}
}
fclose($file);
?>
