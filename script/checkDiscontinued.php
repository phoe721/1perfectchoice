<?
/* Initialization */
require_once('functions.php');

$input = UPLOAD . "skus.txt";
$handle = fopen($input, "r");
if ($handle) {
	while (!feof($handle)) {
		$sku = trim(fgets($handle));
		if (!empty($sku)) {
			$status = is_discontinued($sku) ? "true" : "false";
			$output = $sku . "\t" . $status . PHP_EOL;
			echo $output;
		}
	}
}

function is_discontinued($sku) {
	global $db;
	$result = $db->query("SELECT COUNT(*) FROM product_discontinued WHERE sku = '" . $sku . "'");
	$row = $result->fetch_row();
	$count = $row[0];
	if ($count) {
		return true;
	} else {
		return false;
	}
}
?>
