<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class packages {
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

	public function insert($code, $item_no, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight) {
		if (!$this->exists($code, $item_no)) {
			$result = $this->db->query("INSERT INTO packages (code, item_no, box1_length, box1_width, box1_height, box1_weight, box2_length, box2_width, box2_height, box2_weight, box3_length, box3_width, box3_height, box3_weight, box4_length, box4_width, box4_height, box4_weight, box5_length, box5_width, box5_height, box5_weight) VALUES ('$code', '$item_no', '$box1_length', '$box1_width', '$box1_height', '$box1_weight', '$box2_length', '$box2_width', '$box2_height', '$box2_weight', '$box3_length', '$box3_width', '$box3_height', '$box3_weight', '$box4_legnth', '$box4_width', '$box4_height', '$box4_weight', '$box5_length', '$box5_width', '$box5_height', '$box5_weight')");
			if ($result) {
				$this->output->notice("Item: $item_no, Code: $code packages has been inserted successfully!");
				return true;
			} else {
				$this->output->notice("Item: $item_no, Code: $code failed to insert!");
				return false;
			}
		} else {
			$this->output->notice("Item: $item_no, Code: $code exists!");
			return false;
		}
	}
	
	public function update($code, $item_no, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight) {
		if ($this->exists($code, $item_no)) {
			$result = $this->db->query("UPDATE packages SET box1_length = '$box1_length', box1_width = '$box1_width', box1_height = '$box1_height', box1_weight = '$box1_weight', box2_length = '$box2_length', box2_width = '$box2_width', box2_height = '$box2_height', box2_weight = '$box2_weight', box3_length = '$box3_length', box3_width = '$box3_width', box3_height = '$box3_height', box3_weight = '$box3_weight', box4_length = '$box4_legnth', box4_width = '$box4_width', box4_height = '$box4_height', box4_weight = '$box4_weight ', box5_length = '$box5_length', box5_width = '$box5_width', box5_height = '$box5_height', box5_weight = '$box5_weight' WHERE code = '$code' AND item_no = '$item_no'");
			if ($result) {
				$this->output->notice("Item: $item_no, Code: $code packages has been updated!");
				return true;
			} else {
				$this->output->notice("Item: $item_no, Code: $code failed to update packages!");
				return false;
			}
		} else {
			$this->output->notice("Item: $item_no, Code: $code does not exist!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM packages WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function exists($code, $item_no) {
		$result = $this->db->query("SELECT * FROM packages WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_dimensions($code, $item_no) {
		$dimensions = array();
		if ($this->set_list->check($code, $item_no)) {
			$set = $this->set_list->get_set($code, $item_no);
			$item = $set[0];
			$result = $this->db->query("SELECT box1_length, box1_width, box1_height, box2_length, box2_width, box2_height, box3_length, box3_width, box3_height, box4_length, box4_width, box4_height, box5_length, box5_width, box5_height FROM packages WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($dimensions, $row["box1_length"], $row["box1_width"], $row["box1_height"], $row["box2_length"], $row["box2_width"], $row["box2_height"], $row["box3_length"], $row["box3_width"], $row["box3_height"], $row["box4_length"], $row["box4_width"], $row["box4_height"], $row["box5_length"], $row["box5_width"], $row["box5_height"]);
				$this->output->notice("Item: $item_no, code: $code package dimensions found!");
			} else {
				$this->output->notice("Item: $item_no, code: $code package dimensions not found!");
			}

			return $dimensions;
		} else {
			$result = $this->db->query("SELECT box1_length, box1_width, box1_height, box2_length, box2_width, box2_height, box3_length, box3_width, box3_height, box4_length, box4_width, box4_height, box5_length, box5_width, box5_height FROM packages WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($dimensions, $row["box1_length"], $row["box1_width"], $row["box1_height"], $row["box2_length"], $row["box2_width"], $row["box2_height"], $row["box3_length"], $row["box3_width"], $row["box3_height"], $row["box4_length"], $row["box4_width"], $row["box4_height"], $row["box5_length"], $row["box5_width"], $row["box5_height"]);
				$this->output->notice("Item: $item_no, code: $code package dimensions found!");
			} else {
				$this->output->notice("Item: $item_no, code: $code package dimensions not found!");
			}
	
			return $dimensions;
		}
	}

	public function get_weight($code, $item_no) {
		$weights = array();
		if ($this->set_list->check($code, $item_no)) {
			$total = 0;
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item_no = $set[$i];
				$result = $this->db->query("SELECT box1_weight, box2_weight, box3_weight, box4_weight, box5_weight FROM packages WHERE code = '$code' AND item_no = '$item_no'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					array_push($weights, $row["box1_weight"], $row["box2_weight"], $row["box3_weight"], $row["box4_weight"], $row["box5_weight"]);
					$this->output->notice("Item: $item_no, Code: $code package weights found!");
				} else {
					$this->output->notice("Item: $item_no, Code: $code package weights not found!");
				}
			}

			return $weights;
		} else {
			$result = $this->db->query("SELECT box1_weight, box2_weight, box3_weight, box4_weight, box5_weight FROM packages WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				array_push($weights, $row["box1_weight"], $row["box2_weight"], $row["box3_weight"], $row["box4_weight"], $row["box5_weight"]);
				$this->output->notice("Item: $item_no, Code: $code package weights found!");
			} else {
				$this->output->notice("Item: $item_no, Code: $code package weights not found!");
			}
			return $weights;
		}
	}

	public function get_box_count($code, $item_no) {
			$box_count = 0;
			$result = $this->db->query("SELECT box1_length, box2_length, box3_length, box4_length, box5_length FROM packages WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				for ($i = 1; $i <= 5; $i++) {
					if (!empty($row["box" . $i . "_length"])) $box_count = $i;
				}
				$this->output->notice("Item: $item_no, code: $code with $box_count packages!");
			} else {
				$this->output->notice("Item: $item_no, code: $code not found!");
			}
	
			return $box_count;
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM packages");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table packages!");
		} else {
			$this->output->error("Failed to get record count in table packages!");
		}

		return $count;
	}
}

?>
