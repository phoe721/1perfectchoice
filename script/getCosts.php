<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if(isset($_FILES["file1"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$input_file = AMAZON_UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $input_file) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $input_file";
	$qid = create_queue($uid, $command);

	// Log status
	logger("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$input_file = $argv[2];
	prepare($uid);

	logger("Getting costs...");
	$file1 = fopen($input_file, "r");
	if ($file1) {
		while (!feof($file1)){
			$skus = fgets($file1);
			$skus = trim($skus);
			$vendor_code = get_vendor_code($skus);
			$item_array = get_set_list($skus);
			$total_cost = 0;
			if (check_vendor_code($vendor_code)) {
				for ($i = 0; $i < count($item_array); $i++) {
					$cost = get_cost($vendor_code, $item_array[$i]);
					$total_cost += $cost;
				}
				logger("Total Cost: " . $total_cost);
			} else {
				logger("Invalid Vendor Code!");
			}
			$output = $skus . "\t" . implode("\t", $sku);
			log_result($output);
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	logger("Done!");
}

function check_vendor_code($vendor_code) {
	global $db;
	$result = $db->query("SELECT COUNT(*) FROM vendors WHERE code = '" . $vendor_code . "'");
	if ($result && mysqli_num_rows($result) > 0) return true;

	return false;	
}

function get_vendor_code($sku) {
	if (!empty($sku)) {
		$pieces = explode("-", $sku);
		return $pieces[0];
	} else {
		logger("Invalid SKU: $sku!");
	}
}

function get_item_no($sku) {
	if (!empty($sku)) {
		$vendor_code = get_vendor_code($sku); //ex. AC-00114, result: AC
		$item_str = str_replace($vendor_code . "-", "", $sku); //ex. AC-00114, result: 00114
		$item_array = explode("-", $item_str); //ex. 00114-15, result: 00114, 15
		$first_len = strlen($item_array[0]); //ex. 00114, result: 5
		for ($i = 1; $i < count($item_array); $i++) {
			$current_len = strlen($item_array[$i]);
			if ($first_len > $current_len) {
				$diff = $first_len - $current_len;
				$item_array[$i] = substr($item_array[0], 0, $diff) . $item_array[$i];
			}
		}
		return $item_array;
	} else {
		logger("Invalid SKU: $sku!");
	}
}

function get_set_list($sku) {
	global $db;
	$vendor_code = get_vendor_code($sku);
	$item_str = str_replace($vendor_code . "-", "", $sku);

	$item_array = array();
	$result = $db->query("SELECT sku1, sku2, sku3, sku4, sku5, sku6, sku7, sku8, sku9, sku10 FROM set_list WHERE vendor_code = '" . $vendor_code . "' AND sku = '" . $item_str . "'");
	if ($result) { 
		while ($row = mysqli_fetch_array($result)) {
			for ($i = 1; $i <= 10; $i++) {
				$current_sku = $row['sku' . $i];
				if (!empty($current_sku)) {
					$item_array[$i-1] = $current_sku;
				}
			}
		}
	} else {
		logger("Failed to request database!");
	}

	if (empty($item_array)) $item_array = get_item_no($sku);	
	return $item_array;
}

function get_cost($vendor_code, $item_no) {
	global $db;
	$result = $db->query("SELECT cost FROM costs WHERE vendor_code = '" . $vendor_code . "' AND item_no = '" . $item_no . "'"); 
	if ($result) {
		while ($row = mysqli_fetch_array($result)) {
			$cost = $row['cost'];
			echo "Found item " . $item_no . ": $cost" . PHP_EOL;

			return $cost;
		}
	} else {
		logger("Cost not found!");
	}
}
?>
