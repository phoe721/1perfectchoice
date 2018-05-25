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
			$sku = trim(fgets($file1));
			$vendor_code = get_vendor_code($sku);
			if (check_vendor_code($vendor_code)) {
				$item_array = get_set_list($skus);
				$total_cost = 0;
				for ($i = 0; $i < count($item_array); $i++) {
					$cost = get_cost($vendor_code, $item_array[$i]);
					$total_cost += $cost;
				}
				logger("Total Cost: " . $total_cost);

				$output = $sku . "\t" . $total_cost;
				log_result($output);
			} else {
				logger("Invalid Vendor Code!");
			}
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	logger("Done!");
} else if (isset($argv[1])) {
	$sku = $argv[1];
	$vendor_code = get_vendor_code($sku);
	echo "Vendor code: $vendor_code" . PHP_EOL;
	if (check_vendor_code($vendor_code)) {
		$item_array = get_set_list($vendor_code, $sku);
		$total_cost = 0;
		for ($i = 0; $i < count($item_array); $i++) {
			$cost = get_cost($vendor_code, $item_array[$i]);
			$total_cost += $cost;
			echo "Item No.: " . $item_array[$i] . ", Cost: " . $cost . PHP_EOL;
		}
		echo "Total Cost: " . $total_cost . PHP_EOL;
	} else {
		echo "Invalid Vendor Code!" . PHP_EOL;
	}
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
		$vendor_code = $pieces[0];

		return $vendor_code;
	} else {
		logger("Invalid SKU: $sku!");
		return false;
	}
}

function check_item_no($vendor_code, $item) {
	global $db;
	$result = $db->query("SELECT item_no FROM costs WHERE vendor_code = '" . $vendor_code . "' AND item_no = '" . $item . "'");
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_array($result)) {
			$item_no = $row['item_no'];
			logger("Found item: " . $item_no);
			return true;
		}
	} else {
		logger("Item Not Found: $item");
		return false;
	}
}

function get_set_list($vendor_code, $sku) {
	global $db;
	$item_array = array();
	$item_str = str_replace($vendor_code . "-", "", $sku);
	$item_count = substr_count($item_str, "-") + 1;
	if ($item_count == 1) {
		$item_array[0] = $item_str;
	} else {
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
			$item_array = explode("-", $item_str); //ex. 00114-15, result: 00114, 15
			$first_len = strlen($item_array[0]); //ex. 00114, result: 5
			for ($i = 0; $i < count($item_array); $i++) {
				$current_len = strlen($item_array[$i]);
				if ($first_len > $current_len) {
					$diff = $first_len - $current_len;
					$item_array[$i] = substr($item_array[0], 0, $diff) . $item_array[$i];
				}
			}
		}
	}

	for ($i = 0; $i < count($item_array); $i++) {
		check_item_no($vendor_code, $item_array[$i]);
	}

	return $item_array;
}

function get_cost($vendor_code, $item_no) {
	global $db;
	$cost = -1;
	$result = $db->query("SELECT cost FROM costs WHERE vendor_code = '" . $vendor_code . "' AND item_no = '" . $item_no . "'"); 
	if ($result) {
		while ($row = mysqli_fetch_array($result)) {
			$cost = $row['cost'];
			logger("Found item " . $item_no . ": $cost");
		}
	} else {
		logger("Cost not found!");
	}

	return $cost;
}
?>
