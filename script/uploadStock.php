<?
/* Initialization */
require_once('functions.php');

// First file is vendor stock file
// Second file is vendor SKU filter file
if(isset($_FILES["file1"]) && isset($_POST['uid']) && isset($_POST['vendor'])) {
	$uid = $_POST['uid'];
	$vendor = $_POST['vendor'];
	prepare($uid);	// Prepare directory

	// Move vendor stock file
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$vendor_stock = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $vendor_stock) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $vendor $vendor_stock";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
} else if (isset($_POST['uid']) && isset($_POST['getVendors'])) {
	$result['vendors'] = get_vendors();
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$uid = $argv[1];
	$vendor_code = $argv[2];
	$vendor_stock = $argv[3];
	$vendor_filter = FILTER . "$vendor_code/vendor_sku_filter"; 
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
		log_status("Failed to open $vendor_stock, $inventory");
	}
	fclose($file1);
	fclose($file2);

	/*
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
	 */

	//log_status("Done: Inventory updated with $count records! Random check $pass out of " . TEST_NUMBER . "!");
	log_status("Done!");
}

function get_vendors() {
	global $db;
	$result = array();
	$db_result = $db->query("SELECT code, name FROM vendor");
	if (mysqli_num_rows($db_result) == 0) {
		logger("There are no records in vendor table!");
	} else {
		while ($row = mysqli_fetch_array($db_result)) {
			$code = $row['code'];
			$name = $row['name'];
			$result[$code] = $name;
			//logger("Pushed $name with code $code into result array");
		}
	}

	return $result;
}

function filter_sku_by_file($sku, $filter) {		
	$file = fopen($filter, "r");
	$old_sku = $sku;
	while (($line = fgets($file)) != false) {
		$replace = trim($line);
		$sku = preg_replace('/' . $replace . '$/', '', $sku);
	}
	fclose($file);

	logger("Filtered $old_sku to $sku");
	return $sku;
}
?>
