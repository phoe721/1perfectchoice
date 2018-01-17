<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if(isset($_FILES["file1"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$input_file = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $input_file) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $input_file";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$input_file = $argv[2];
	prepare($uid);

	log_status("Calculate shipping...");
	$file1 = fopen($input_file, "r");
	if ($file1) {
		while (!feof($file1)){
			$line = fgets($file1);
			$line = trim($line);
			if (!empty($line)) {
				list($sku, $cost, $weight, $length, $width, $height)= explode("\t", $line);
				$ups_cost = getUPSCost($cost, $length, $width, $height, $weight);
				$trucking_cost = getTruckingCost($weight);
				if ($ups_cost > 0) {
					$output = "$sku, $ups_cost";
				} else {
					$output = "$sku, $trucking_cost";
				}	
				log_result($output);
			}
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	log_status("Done!");
}

function getUPSCost($cost, $length, $width, $height, $weight) {
	global $db;
	$shipping_cost = -1;
	$length += 2;
	$width += 2;
	$height += 2;
	$dimension_weight = round(($length * $width * $height)/166, 0);
	$actual_weight = max($weight, $dimension_weight);
	$girth = (2 * $width) + (2 * $height);
	$measurement = $length + $girth;
	logger("Dimensiona weight is $dimension_weight");
	logger("Actual weight is $actual_weight");
	logger("Girth is $girth");
	logger("Measurement is $measurement");

	if ($actual_weight > UPS_WEIGHT_LIMIT) {
		logger("Actual weight is over UPS weight limit");
	} else if ($measurement > UPS_MEASUREMENT_LIMIT) { 
		logger("Measurement is over UPS measurement limit");
	} else if ($length > UPS_LENGTH_LIMIT) {
		logger("Length is over UPS length limit");
	} else {
		$db_result = $db->query("SELECT cost FROM UPS_cost WHERE weight = '$weight'");
		if (mysqli_num_rows($db_result) > 0) {
			while ($row = mysqli_fetch_array($result)) {
				$shipping_cost = $row['cost'];
				logger("UPS zone 8 cost is $shipping_cost");
			}
		}

		$fuel_surcharge = round($shipping_cost * (UPS_FUEL_SURCHAGE / 100), 2);
		logger("Fuel surcharge is $fuel_surcharge");
		if ($measurement >= UPS_LARGE_PACKAGE_LIMIT) {
			logger("Measurement is over UPS large package limit");
			$shipping_cost += UPS_LARGE_PACKAGE_COST;
		}	
		$cost = ceil($cost / 100) * 100;
		$insurance = UPS_BASE_INSURANCE_COST + (round($cost / UPS_BASE_INSURANCE_COVERAGE, 0) * UPS_INSURANCE_RATE);
		logger("Insurance is $insurance");
		$shipping_cost += $insurance;
	}

	logger("UPS cost is $shipping_cost");
	return $shipping_cost;
}

function getTruckingCost($weight) {
	$shipping_cost = -1;
	if ($weight <= TRUCKING_BASE_WEIGHT) {
		$shipping_cost = TRUCKING_BASE_COST;
	} else {
		$shipping_cost = round($weight * 1.5, 2);
	}

	logger("Trucking cost is $shipping_cost");
	return $shipping_cost;
}

?>
