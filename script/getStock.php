<?
// Initialization
require_once('functions.php');

// Main Program
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4])) {
	$uid = $argv[1];
	$file1 = $argv[2];
	$file2 = $argv[3];
	process_request($uid, $file1, $file2, $file3);
} else if(isset($_FILES["file1"]) && isset($_FILES["file2"]) && isset($_FILES["file3"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	$file1 = $_FILES["file1"];
	$file2 = $_FILES["file2"];
	$file3 = $_FILES["file3"];
	get_request($uid, $file1, $file2, $file3);
}

// Get request
function get_request($uid, $file1, $file2, $file3) {
	prepare($uid);	// Prepare directory
	$destination1 = UPLOAD . $uid . '/' . basename($file1["name"]);
	$destination2 = UPLOAD . $uid . '/' . basename($file2["name"]);
	$destination3 = UPLOAD . $uid . '/' . basename($file3["name"]);
	$command = "/usr/bin/php " . __FILE__ . " $uid $destination1 $destination2 $destination3";

	move_file($uid, $file1, $destination1);
	move_file($uid, $file2, $destination2);
	move_file($uid, $file3, $destination3);
	create_queue($uid, $command);
}

// Process request
function process_request($uid, $file1, $file2, $file3) {
	prepare($uid);
	get_stock($uid, $file1, $file2, $file3);
}

// Get Stock
// 1. file1 - SKUs
// 2. file2 - Filter
// 3. file3 - Set List
function get_stock($uid, $file1, $file2, $file3) {
	// Define File Paths
	$found_list = DOWNLOAD. $uid . "/found_list.txt";
	$not_found_list = DOWNLOAD . $uid . "/not_found_list.txt";
	$found = $notFound = 0;

	$f1 = fopen($file1, "r");
	$f2 = fopen($found_list, "w+");
	$f3 = fopen($not_found_list, "w+");
	if ($f1 && $f2 && $f3) {
		while (!feof($f1)){
			$line = fgets($f1);
			$data = explode("\t", $line);
			if (!empty($data[0])) {
				$sku = trim($data[0]);
				$newSKU = filter_sku_by_file($sku, $file2);
				$qty = query_stock($newSKU);
				if ($qty == -1) {
					$qty2 = check_set_sku_by_file($newSKU, $file3);
					if ($qty2 == -1) {
						$str = $sku;
						fwrite($f3, $str . PHP_EOL);
						$notFound++;
					} else {
						$str = $sku . "\t" . $qty2; 
						fwrite($f2, $str . PHP_EOL);
						$found++;
					}
				} else {
					$str = $sku . "\t" . $qty; 
					fwrite($f2, $str . PHP_EOL);
					$found++;
				}
			}
		}
		logger("Found: $found, Not Found: $notFound");
	} else {
		logger("Failed to open $file1, $found_list, and $not_found_list");
	}
	fclose($f1);
	fclose($f2);
	fclose($f3);

	if ($found > 0) log_link_file($found_list);
	if ($notFound > 0) log_link_file($not_found_list);
	log_status("Done: Found $found, Not found $notFound");
}

// Query stock
function query_stock($sku) {
	global $db;
	$qty = -1;
	$result = $db->query("SELECT quantity FROM inventory WHERE mpn = '" . $sku . "'");
	if (mysqli_num_rows($result) == 0) {
		// Found nothing
	} else {
		$row = mysqli_fetch_array($result);
		$qty = $row['quantity'];
	}

	logger("Lookup $sku found $qty"); 
	return $qty;
}

// Filter SKU by file
function filter_sku_by_file($sku, $path) {
	$f = fopen($path, "r");
	$newSKU = $sku;
	while (!feof($f)) {
		$line = fgets($f);
		if (!empty($line)) {
			$data = explode(",", $line);
			$exp = trim($data[0]);
			$replace = trim($data[1]);
			$newSKU = preg_replace('/' . $exp . '/', "$replace", $newSKU);	
			logger("After applying $exp to $replace filter: $sku => $newSKU"); 
		}
	}
	fclose($f);

	logger("After applying filter: $sku => $newSKU"); 
	return $newSKU;
}

// Check if SKU is a set
function check_set_sku_by_file($sku, $path) {
	$f = fopen($path, "r");
	$qtyArray = array();
	while (!feof($f)) {
		$line = fgets($f);
		if (!empty($line)) {
			$skus = explode("\t", $line);
			$setSKU = trim($skus[0]);
			if ($sku == $setSKU) {
				for ($i = 1; $i < count($skus); $i++) {
					$skus[$i] = trim($skus[$i]);
					$qty = query_stock($skus[$i]);
					array_push($qtyArray, $qty);	
				}
			}
		}
	}
	fclose($f);

	if (!empty($qtyArray)) {
		logger("Lookup $sku found " . min($qtyArray));
		return min($qtyArray);
	} else {
		logger("Lookup $sku not found");
		return -1;
	}
}

?>
