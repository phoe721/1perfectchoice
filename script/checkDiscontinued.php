<?
require_once("class/discontinued.php");
$dis = new discontinued();
$file = fopen(UPLOAD . "input.txt", "r");
while(!feof($file)) {
	$line = trim(fgets($file));
	if (!empty($line)) {
		list($code, $item_no) = explode("-", $line);
		$dis->check($code, $item_no);
	}
}
fclose($file);

?>
