<?
/* Initialization */
require_once("database.php");
require_once("helper_functions.php");

class manufacturing_country {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $country) {
		$country = $this->db->real_escape_string($country);
		$result = $this->db->query("INSERT INTO manufacturing_country (code, item_no, country) VALUES ('$code', '$item_no', '$country')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $country) {
		$country = $this->db->real_escape_string($country);
		$result = $this->db->query("UPDATE manufacturing_country SET country = '$country' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Country updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update country!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM manufacturing_country WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_origin($code, $item) {
		$country = '';
		$result = $this->db->query("SELECT country FROM manufacturing_country WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$country = $row['country'];
			$this->output->info("Item: $item, code: $code - Country: $country found!");
		} else {
			$this->output->info("Item: $item, code: $code - Country not found!");
		}

		return $country;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM manufacturing_country WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM manufacturing_country");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table product!");
		} else {
			$this->output->error("Failed to get record count in table product!");
		}

		return $count;
	}
}
?>
