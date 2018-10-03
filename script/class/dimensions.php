<?
/* Initialization */
require_once("database.php");

class dimensions {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no, $length, $width, $height, $weight) {
		$result = $this->db->query("INSERT INTO dimensions (code, item_no, ship_length, ship_width, ship_height, ship_weight) VALUES ('$code', '$item_no', '$length', '$width', '$height', '$wegith')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code dimensions has been inserted successfully!");
			return true;
		} else {
			$this->output->error("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update_dimensions($code, $item_no, $length, $width, $height) {
		$result = $this->db->query("UPDATE dimensions SET ship_length = '$length', ship_width = '$width', ship_height = '$height' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code dimensions has been updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code failed to update dimensions!");
			return false;
		}
	}

	public function update_weight($code, $item_no, $weight) {
		$result = $this->db->query("UPDATE dimensions SET weight = '$weight' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code weight has been updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code failed to update weight!");
			return false;
		}
	}

	public function get_dimensions($code, $item_no) {
		$dim = array();
		$result = $this->db->query("SELECT ship_length, ship_width, ship_height FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$dim["length"] = $row["ship_length"];
			$dim["width"] = $row["ship_width"];
			$dim["height"] = $row["ship_height"];
			$this->output->info("item: $item_no, code: $code dimensions $length x $width x $height!");
		} else {
			$this->output->info("item: $item_no, code: $code dimensions not found!");
		}

		return $dim;
	}

	public function get_weight($code, $item_no) {
		$weight = -1;
		$result = $this->db->query("SELECT ship_weight FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$weight = $row["ship_weight"];
			$this->output->info("Item: $item_no, Code: $code has weight $weight!");
		} else {
			$this->output->info("Item: $item_no, Code: $code weight not found!");
		}
		return $weight;
	}

	public function truncate_table() {
		$result = $this->db->query("TRUNCATE TABLE dimensions");
		if ($result) {
			$this->output->info("Table truncated!");
			return true;
		} else {
			$this->output->error("Failed to truncate!");
			return false;
		}
	}
	
	public function update_by_file($filePath) {
		$result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE dimensions");
		if ($result) {
			$this->output->info("Table updated with $filePath!");
			return true;
		} else {
			$this->output->error("Failed to update table with $filePath!");
			return false;
		}
	}
	
	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM dimensions");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->info("There are $count records in table dimensions!");
		} else {
			$this->output->error("Failed to get record count in table dimensions!");
		}

		return $count;
	}
}

?>
