<?
/* Initialization */
require_once('functions.php');

// First file is vendor stock file
// Second file is vendor SKU filter file
if(isset($_FILES["file1"]) && isset($_FILES["file2"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move vendor stock file
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$vendor_stock = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $vendor_stock) ;

	// Move vendor filter file 
	$tmp_file2 = $_FILES["file2"]["tmp_name"];
	$vendor_filter = UPLOAD . $uid . '/' . basename($_FILES["file2"]["name"]);
	move_uploaded_file($tmp_file2, $vendor_filter) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $vendor_stock $vendor_filter";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$uid = $argv[1];
	$vendor_stock = $argv[2];
	$vendor_filter = $argv[3];
	prepare($uid);

	log_status("Updating inventory...");
	$inventory = UPLOAD . $uid . '/' . "inventory.txt";
	$file1 = fopen($vendor_stock, "r");
	$file2 = fopen($inventory, "w+");
	if ($file1 && $file2) {
		while (!feof($file1)){
			$line = fgets($file1);
			$data = explode("\t", $line);
			if (!empty($data[0])) {
				$sku = trim($data[0]);
				$qty = trim($data[1]);
				$qty = empty($qty) ? 0 : $qty; // Change empty qty to zero
				$qty = ($qty < 0) ? 0 : $qty; // Change negative numbers to zero
				$sku = filter_sku_by_file($sku, $vendor_filter);
				$str = $sku . "\t" . $qty;
				fwrite($file2, $str . PHP_EOL);
			}
		}
	} else {
		logger("Failed to open $vendor_stock, $inventory");
	}
	fclose($file1);
	fclose($file2);

	truncate_inventory();
	update_inventory_by_file($inventory);
	$count = inventory_record_count();

	log_status("Random select from database to check validity...");
	$pass = 0;
	$lines = file($vendor_stock);
	if ($lines) {
		$selected = array_rand($lines, TEST_NUMBER);
		foreach ($selected as $number) {
			$data = explode("\t", $lines[$number]);
			if (!empty($data[0])) {
				$sku = trim($data[0]);
				$sku = filter_sku_by_file($sku, $vendor_filter);
				$qty = trim($data[1]);
				$qty = empty($qty) ? 0 : $qty;
				$qty = ($qty < 0) ? 0 : $qty;
				$found = query_stock($sku);
				if ($qty == $found) {
					$pass++;
				} else {
					logger("Failed: $sku found $found, but should be $qty");
				}
			}
		}
	} else {
		logger("Failed to open $vendor_stock");
	}

	log_status("Done: Inventory updated with $count records! Random check $pass out of " . TEST_NUMBER . "!");
}

?>
