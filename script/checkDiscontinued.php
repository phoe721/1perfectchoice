<?
require_once("class/discontinued.php");
$dis = new discontinued();
$dis->insert('CO', '105543');
$dis->insert('CO', '190127');
$dis->insert('CO', '190129');
/*
$file = fopen(UPLOAD . "input.txt", "r");
while(!feof($file)) {
	$line = trim(fgets($file));
	if (!empty($line)) {
		list($code, $item_no) = explode("-", $line);
		$dis->check($code, $item_no);
	}
}
fclose($file);
 */

?>
