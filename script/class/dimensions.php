<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class dimensions {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
		$this->set_list = new set_list();
	}

	public function insert($code, $item_no, $length, $width, $height, $weight) {
		$result = $this->db->query("INSERT INTO dimensions (code, item_no, length, width, height, weight) VALUES ('$code', '$item_no', '$length', '$width', '$height', '$wegith')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code dimensions has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update($code, $item_no, $length, $width, $height, $weight) {
		$result = $this->db->query("UPDATE dimensions SET length = '$length', width = '$width', height = '$height', weight = '$weight' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code dimensions has been updated!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to update dimensions!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code exists!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code not exist!");
			return false;
		}
	}

	public function get_dimensions($code, $item_no) {
		$dimensions = array();
		if ($this->set_list->check($code, $item_no)) {
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$dimensions = array_merge($dimensions, $this->get_dimensions($code, $item));
			}

			return $dimensions;
		} else {
			$result = $this->db->query("SELECT length, width, height FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($dimensions, $row["length"], $row["width"], $row["height"]);
				$this->output->notice("Item: $item_no, code: $code dimensions found!");
			} else {
				$this->output->notice("Item: $item_no, code: $code dimensions not found!");
			}

			return $dimensions;
		}
	}

	public function get_weight($code, $item_no) {
		$weights = array();
		if ($this->set_list->check($code, $item_no)) {
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$weights = array_merge($weights, $this->get_weight($code, $item));
			}

			return $weights;
		} else {
			$result = $this->db->query("SELECT weight FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($weights, $row["weight"]);
				$this->output->notice("Item: $item_no, Code: $code weight found!");
			} else {
				array_push($weights, 0);
				$this->output->notice("Item: $item_no, Code: $code weight not found!");
			}

			return $weights;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM dimensions");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table dimensions!");
		} else {
			$this->output->error("Failed to get record count in table dimensions!");
		}

		return $count;
	}
}

?>
