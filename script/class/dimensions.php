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
		if (!$this->exists($code, $item_no)) {
			$result = $this->db->query("INSERT INTO dimensions (code, item_no, length, width, height, weight) VALUES ('$code', '$item_no', '$length', '$width', '$height', '$wegith')");
			if ($result) {
				$this->output->notice("Item: $item_no, Code: $code dimensions has been inserted successfully!");
				return true;
			} else {
				$this->output->notice("Failed to insert $item_no!");
				return false;
			}
		} else {
			$this->output->notice("Item: $item_no, Code: $code exists!");
			return false;
		}
	}
	
	public function update($code, $item_no, $length, $width, $height, $weight) {
		if ($this->exists($code, $item_no)) {
			$result = $this->db->query("UPDATE dimensions SET length = '$length', width = '$width', height = '$height', weight = '$weight' WHERE code = '$code' AND item_no = '$item_no'");
			if ($result) {
				$this->output->notice("Item: $item_no, Code: $code dimensions has been updated!");
				return true;
			} else {
				$this->output->notice("Item: $item_no, Code: $code failed to update dimensions!");
				return false;
			}
		} else {
			$this->output->notice("Item: $item_no, Code: $code does not exist! Inserting it!");
			return $this->insert($code, $item_no, $length, $width, $height, $weight);
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

	public function exists($code, $item_no) {
		$result = $this->db->query("SELECT * FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_dimensions($code, $item_no) {
		$dim = array();
		if ($this->set_list->check($code, $item_no)) {
			$set = $this->set_list->get_set($code, $item_no);
			$item = $set[0];
			$result = $this->db->query("SELECT length, width, height FROM dimensions WHERE code = '$code' AND item_no = '$item'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($dim, $row["length"], $row["width"], $row["height"]);
				$this->output->notice("Item: $item_no, code: $code dimensions " . $dim[0] . " x " . $dim[1] . " x " . $dim[2] . "!");
			} else {
				$this->output->notice("Item: $item_no, code: $code dimensions not found!");
			}

			return $dim;
		} else {
			$result = $this->db->query("SELECT length, width, height FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($dim, $row["length"], $row["width"], $row["height"]);
				$this->output->notice("Item: $item_no, code: $code dimensions " . $dim[0] . " x " . $dim[1] . " x " . $dim[2] . "!");
			} else {
				$this->output->notice("Item: $item_no, code: $code dimensions not found!");
			}
	
			return $dim;
		}
	}

	public function get_weight($code, $item_no) {
		$weight = 0;
		if ($this->set_list->check($code, $item_no)) {
			$total = 0;
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item_no = $set[$i];
				$result = $this->db->query("SELECT weight FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$weight = $row["weight"];
					$total += $weight;
					$this->output->notice("Item: $item_no, Code: $code has weight $weight!");
				} else {
					$this->output->notice("Item: $item_no, Code: $code weight not found!");
				}
			}

			return $total;
		} else {
			$result = $this->db->query("SELECT weight FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$weight = $row["weight"];
				$this->output->notice("Item: $item_no, Code: $code has weight $weight!");
			} else {
				$this->output->notice("Item: $item_no, Code: $code weight not found!");
			}
			return $weight;
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
