<?	
/* Initialization */
require_once("discontinued.php");
$dis = new discontinued();

$handle = fopen(UPLOAD . "check.txt", "r");
while (!feof($handle)) {
	$line = trim(fgets($handle));
	if (!empty($line)) {
		$pos = strpos($line, "-");
		$code = substr($line, 0, $pos);
		$item_no = substr($line, $pos + 1);
		//echo $code . ", " . $item_no . PHP_EOL;
		$dis->check($code, $item_no);
	}
}
fclose($handle);
?>
