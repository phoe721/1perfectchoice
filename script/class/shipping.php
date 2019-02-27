<?
/* Initialization */
require_once("database.php");

class shipping {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function getCuft($length, $width, $height) {
		$cuft = ceil(($length / 12) * ($width / 12) * ($height / 12));
		$this->output->notice("Cubic feet is $cuft");

		return $cuft;	
	}

	public function getPalletCount($cuft) {
		$pallet_count = ceil($cuft / MAX_CUFT_ON_PALLET);
		$this->output->notice("Pallet count is $pallet_count");

		return $pallet_count;
	}

	public function getTruckingCost($weight) {
		if ($weight <= TRUCKING_BASE_WEIGHT) {
			$trucking_cost = TRUCKING_BASE_COST;
		} else {
			$trucking_cost = round($weight * TRUCKING_RATE, 2);
		}

		$this->output->notice("Trucking cost is $trucking_cost");
		return $trucking_cost;
	}

	public function getUPSCost($cost, $length, $width, $height, $weight) {
		$ups_cost = -1;
		$dimension_weight = round(($length * $width * $height)/UPS_DIMENSION_WEIGHT_DIVIDER, 0);
		$actual_weight = max($weight, $dimension_weight);
		$girth = round((2 * $width) + (2 * $height), 0);
		$measurement = $length + $girth;
		$this->output->notice("Dimensiona weight is $dimension_weight");
		$this->output->notice("Actual weight is $actual_weight");
		$this->output->notice("Girth is $girth");
		$this->output->notice("Measurement is $measurement");
	
		if ($actual_weight > UPS_WEIGHT_LIMIT) {
			$this->output->notice("Actual weight is over UPS weight limit!");
		} else if ($measurement > UPS_MEASUREMENT_LIMIT) { 
			$this->output->notice("Measurement is over UPS measurement limit!");
		} else if ($length > UPS_LENGTH_LIMIT) {
			$this->output->notice("Length is over UPS length limit!");
		} else {
			$result = $this->db->query("SELECT cost FROM UPS_cost WHERE weight = '$actual_weight'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$ups_cost = $row['cost'];
				$this->output->notice("UPS zone 8 cost is $ups_cost");
	
				// Fuel Surcharge
				$fuel_surcharge = round($ups_cost * UPS_FUEL_SURCHARGE / 100, 2);
				$this->output->notice("Fuel surcharge is $fuel_surcharge");
	
				// Large Package
				$large_package_cost = 0;
				if ($measurement >= UPS_LARGE_PACKAGE_LIMIT) {
					$this->output->notice("Measurement is over UPS large package limit!");
					$large_package_cost = UPS_LARGE_PACKAGE_COST;
				}	
	
				// Insurance
				$cost = ceil($cost / 100) * 100;
				$insurance = UPS_BASE_INSURANCE_COST + (ceil($cost / UPS_BASE_INSURANCE_COVERAGE) * UPS_INSURANCE_RATE);
				$this->output->notice("Insurance is $insurance");
	
				// Total cost
				$ups_cost += $fuel_surcharge + $large_package_cost + $insurance;
				$this->output->notice("UPS cost is $ups_cost");
			} else {
				$this->output->error("Failed to look up UPS cost in database!");
			}
		}
	
		return $ups_cost;
	}
}

?>
