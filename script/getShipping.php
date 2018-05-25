<?
/* Initialization */
require_once('functions.php');
require_once('getItemsCosts.php');

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
} else if (isset($_POST["sku"]) && isset($_POST["cost"]) && isset($_POST["weight"]) && isset($_POST["length"]) && isset($_POST["width"]) && isset($_POST["height"])) {
	$sku = $_POST["sku"];
	$cost = $_POST["cost"];
	$weight = $_POST["weight"];
	$length = $_POST["length"];
	$width = $_POST["width"];
	$height = $_POST["height"];
	logger("Processing: $sku, $cost, $weight, $length, $width, $height");

	$weight = round($weight, 0);
	$length = round($length, 0) + 2;
	$width = round($width, 0) + 2;
	$height = round($height, 0) + 2;
	$ups_cost = getUPSCost($cost, $length, $width, $height, $weight);
	$trucking_cost = getTruckingCost($weight);
	$cuft = getCuft($length, $width, $height);
	$pallet_count = getPalletCount($cuft);

	// Output status
	$output = "SKU: $sku, UPS Cost: $ups_cost, Trucking Cost: $trucking_cost, Cubic Feet: $cuft, Pallet: $pallet_count";
	$result['status'] = $output;
	logger($output);
	echo json_encode($result);
}

if (isset($argv[1])) { 
	$sku = $argv[1];

} else if (isset($argv[1]) && isset($argv[2])) {
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
				logger("Processing: $sku, $cost, $weight, $length, $width, $height");
				$weight = round($weight, 0);
				$length = round($length, 0) + 2;
				$width = round($width, 0) + 2;
				$height = round($height, 0) + 2;
				$ups_cost = getUPSCost($cost, $length, $width, $height, $weight);
				$trucking_cost = getTruckingCost($weight);
				$cuft = getCuft($length, $width, $height);
				$pallet_count = getPalletCount($cuft);

				if ($ups_cost > 0) {
					$output = "$sku\t$ups_cost\t$cuft\t$pallet_count";
				} else {
					$output = "$sku\t$trucking_cost\t$cuft\t$pallet_count";
				}	
				log_result($output);
			}
		}
	} else {
		log_status("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	log_status("Done!");
}

function getCuft($length, $width, $height) {
	$cuft = ceil(($length / 12) * ($width / 12) * ($height / 12));
	return $cuft;	
}

function getPalletCount($cuft) {
	$pallet_count = ceil($cuft / MAX_CUFT_ON_PALLET);
	return $pallet_count;
}

function getTruckingCost($weight) {
	$trucking_cost = -1;
	if ($weight <= TRUCKING_BASE_WEIGHT) {
		$trucking_cost = TRUCKING_BASE_COST;
	} else {
		$trucking_cost = round($weight * 1.5, 2);
	}

	logger("Trucking cost is $trucking_cost");
	return $trucking_cost;
}

function getUPSCost($cost, $length, $width, $height, $weight) {
	global $db;
	$ups_cost = -1;
	$dimension_weight = round(($length * $width * $height)/UPS_DIMENSION_WEIGHT_DIVIDER, 0);
	$actual_weight = max($weight, $dimension_weight);
	$girth = round((2 * $width) + (2 * $height), 0);
	$measurement = $length + $girth;
	logger("Dimensiona weight is $dimension_weight");
	logger("Actual weight is $actual_weight");
	logger("Girth is $girth");
	logger("Measurement is $measurement");

	if ($actual_weight > UPS_WEIGHT_LIMIT) {
		logger("Actual weight is over UPS weight limit!");
	} else if ($measurement > UPS_MEASUREMENT_LIMIT) { 
		logger("Measurement is over UPS measurement limit!");
	} else if ($length > UPS_LENGTH_LIMIT) {
		logger("Length is over UPS length limit!");
	} else {
		$db_result = $db->query("SELECT cost FROM UPS_cost WHERE weight = '$actual_weight'");
		if (mysqli_num_rows($db_result) > 0) {
			while ($row = mysqli_fetch_array($db_result)) {
				$ups_cost = $row['cost'];
				logger("UPS zone 8 cost is $ups_cost");
			}

			// Fuel Surcharge
			$fuel_surcharge = round($ups_cost * UPS_FUEL_SURCHARGE / 100, 2);
			logger("Fuel surcharge is $fuel_surcharge");

			// Large Package
			$large_package_cost = 0;
			if ($measurement >= UPS_LARGE_PACKAGE_LIMIT) {
				logger("Measurement is over UPS large package limit!");
				$large_package_cost = UPS_LARGE_PACKAGE_COST;
			}	

			// Insurance
			$cost = ceil($cost / 100) * 100;
			$insurance = UPS_BASE_INSURANCE_COST + (ceil($cost / UPS_BASE_INSURANCE_COVERAGE) * UPS_INSURANCE_RATE);
			logger("Insurance is $insurance");

			// Total cost
			$ups_cost += $fuel_surcharge + $large_package_cost + $insurance;
			logger("UPS cost is $ups_cost");
		} else {
			log_status("Failed to look up UPS cost in database!");
		}
	}

	return $ups_cost;
}

?>
