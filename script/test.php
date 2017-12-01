<?	
/* Initialization */
require_once("functions.php");

global $db;
$input_file = UPLOAD . "input.txt";
$file = fopen($input_file, "r");

while (!feof($file)) {
	$line = fgets($file);
	if (!empty($line)) {
		$sku = trim($line);
		echo $sku . PHP_EOL;
		$result = $db->query("INSERT INTO product_discontinue (sku, discontinue) VALUES ('$sku', 1)");
		if ($result) {
			echo "Insert $sku successfully!" . PHP_EOL;
		} else {
			echo "Insert $sku failed!" . PHP_EOL;
		}
	}
}

fclose($file);

?>
