<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if (isset($_FILES["file1"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	logger("File Upload Error: " . $_FILES["file1"]["error"]);
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$sku_file = UPLOAD . $uid . '/'. basename($_FILES["file1"]["name"]);
	if (move_uploaded_file($tmp_file1, $sku_file)) { 
		logger("SKU file path: $sku_file.");
	} else {
		logger("Failed to upload sku file: $sku_file.");
	}

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $sku_file";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$sku_file = $argv[2];
	prepare($uid);

	truncate_discontinued_table();
	update_discontinued_table_by_file($sku_file);
	$count = discontinued_table_record_count();
	log_status("Done! $count records are updated!"); 
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
