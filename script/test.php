<?	
/* Initialization */
require_once("functions.php");

global $db;
$input_file = UPLOAD . "input.txt";
$file = fopen($input_file, "r");

while (!feof($file)) {
	$line = fgets($file);
	$sku = trim($line);
	$result = $db->query("UPDATE product SET discontinued = 1 WHERE sku = '$sku'");
	if ($result) {
		echo "$sku is updated to discontinued!" . PHP_EOL;
	} else {
		echo "$sku is not found!" . PHP_EOL;
	}
}

fclose($file);

?>
