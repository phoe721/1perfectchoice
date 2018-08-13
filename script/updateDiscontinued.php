<?
/* Initialization */
require_once('functions.php');

if (isset($argv[1])) {
	$sku_file = $argv[1];

	truncate_discontinued_table();
	update_discontinued_table_by_file($sku_file);
	$count = discontinued_table_record_count();
	logger("Done! $count records are updated!"); 
}

function truncate_discontinued_table() {
	global $db;
	$result = $db->query("TRUNCATE TABLE product_discontinued");
	if ($result) {
		logger("Discontinued table truncated!");
	} else {
		logger("Failed to truncate discontinued table!");
	}
}

function update_discontinued_table_by_file($filePath) {
	global $db;
	$result = $db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE product_discontinued");
	if ($result) {
		logger("Discontinued table updated with $filePath!");
	} else {
		logger("Failed to update discontinued table with $filePath!");
	}
}

function discontinued_table_record_count() {
	global $db;
	$result = $db->query("SELECT COUNT(*) FROM product_discontinued");
	$row = $result->fetch_row();
	$count = $row[0];
	return $count;
}
?>
