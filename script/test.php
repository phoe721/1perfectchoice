<?	
/* Initialization */
require_once('functions.php');

$input_file = UPLOAD . "input.csv";
$file = fopen($input_file, "r");
if ($file) {
	while (($line = fgets($file)) !== false) {
		$line = trim($line);
		echo $line . PHP_EOL;
		$item = explode(",", $line);
		if ($result) {
			echo "$sku is updated" . PHP_EOL;
		}

		// Remove SKU
		/*
		$result = $db->query("DELETE FROM product WHERE sku = '" . $sku . "'");
		if ($result) {
			echo "$sku is removed" . PHP_EOL;
		} else {
			echo "$sku is not removed" . PHP_EOL;
		}
		*/
	}
}
fclose($file);

?>
