<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("set_list.php");
require_once("helper_functions.php");

class packages {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->set_list = new set_list();
	}

	public function insert($code, $item_no, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight, $box6_length, $box6_width, $box6_height, $box6_weight, $box7_length, $box7_width, $box7_height, $box7_weight, $box8_length, $box8_width, $box8_height, $box8_weight, $box9_length, $box9_width, $box9_height, $box9_weight, $box10_length, $box10_width, $box10_height, $box10_weight, $box11_length, $box11_width, $box11_height, $box11_weight) {
			$result = $this->db->query("INSERT INTO packages (code, item_no, box1_length, box1_width, box1_height, box1_weight, box2_length, box2_width, box2_height, box2_weight, box3_length, box3_width, box3_height, box3_weight, box4_length, box4_width, box4_height, box4_weight, box5_length, box5_width, box5_height, box5_weight, box6_length, box6_width, box6_height, box6_weight, box7_length, box7_width, box7_height, box7_weight, box8_length, box8_width, box8_height, box8_weight, box9_length, box9_width, box9_height, box9_weight, box10_length, box10_width, box10_height, box10_weight, box11_length, box11_width, box11_height, box11_weight) VALUES ('$code', '$item_no', '$box1_length', '$box1_width', '$box1_height', '$box1_weight', '$box2_length', '$box2_width', '$box2_height', '$box2_weight', '$box3_length', '$box3_width', '$box3_height', '$box3_weight', '$box4_legnth', '$box4_width', '$box4_height', '$box4_weight', '$box5_length', '$box5_width', '$box5_height', '$box5_weight', '$box6_length', '$box6_width', '$box6_height', '$box6_weight', '$box7_length', '$box7_width', '$box7_height', '$box7_weight', '$box8_length', '$box8_width', '$box8_height', '$box8_weight', '$box9_length', '$box9_width', '$box9_height', '$box9_weight', '$box10_length', '$box10_width', '$box10_height', '$box10_weight', '$box11_length', '$box11_width', '$box11_height', '$box11_weight')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Packages has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function insert_dimensions($code, $item_no, $box1_length, $box1_width, $box1_height, $box2_length, $box2_width, $box2_height, $box3_length, $box3_width, $box3_height, $box4_legnth, $box4_width, $box4_height, $box5_length, $box5_width, $box5_height, $box6_length, $box6_width, $box6_height, $box7_length, $box7_width, $box7_height, $box8_length, $box8_width, $box8_height, $box9_length, $box9_width, $box9_height, $box10_length, $box10_width, $box10_height, $box11_legnth, $box11_width, $box11_height) {
			$result = $this->db->query("INSERT INTO packages (code, item_no, box1_length, box1_width, box1_height, box2_length, box2_width, box2_height, box3_length, box3_width, box3_height, box4_length, box4_width, box4_height, box5_length, box5_width, box5_height, box6_length, box6_width, box6_height, box7_length, box7_width, box7_height, box8_length, box8_width, box8_height, box9_length, box9_width, box9_height, box10_length, box10_width, box10_height, box11_length, box11_width, box11_height) VALUES ('$code', '$item_no', '$box1_length', '$box1_width', '$box1_height', '$box2_length', '$box2_width', '$box2_height', '$box3_length', '$box3_width', '$box3_height', '$box4_legnth', '$box4_width', '$box4_height', '$box5_length', '$box5_width', '$box5_height', '$box6_length', '$box6_width', '$box6_height', '$box7_length', '$box7_width', '$box7_height', '$box8_length', '$box8_width', '$box8_height', '$box9_length', '$box9_width', '$box9_height', '$box10_length', '$box10_width', '$box10_height', '$box11_length', '$box11_width', '$box11_height')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Packages has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}

	public function insert_weights($code, $item_no, $box1_weight, $box2_weight, $box3_weight, $box4_weight, $box5_weight, $box6_weight, $box7_weight, $box8_weight, $box9_weight, $box10_weight, $box11_weight) {
		$result = $this->db->query("INSERT INTO packages (code, item_no, box1_weight, box2_weight, box3_weight, box4_weight, box5_weight, box6_weight, box7_weight, box8_weight, box9_weight, box10_weight, box11_weight) VALUES ('$code', '$item_no', '$box1_weight', '$box2_weight', '$box3_weight', '$box4_weight', '$box5_weight', '$box6_weight', '$box7_weight', '$box8_weight', '$box9_weight', '$box10_weight', '$box11_weight')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Packages has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}

	public function update($code, $item_no, $box1_length, $box1_width, $box1_height, $box1_weight, $box2_length, $box2_width, $box2_height, $box2_weight, $box3_length, $box3_width, $box3_height, $box3_weight, $box4_legnth, $box4_width, $box4_height, $box4_weight, $box5_length, $box5_width, $box5_height, $box5_weight, $box6_length, $box6_width, $box6_height, $box6_weight, $box7_length, $box7_width, $box7_height, $box7_weight, $box8_length, $box8_width, $box8_height, $box8_weight, $box9_length, $box9_width, $box9_height, $box9_weight, $box10_length, $box10_width, $box10_height, $box10_weight, $box11_length, $box11_width, $box11_height, $box11_weight) {
			$result = $this->db->query("UPDATE packages SET box1_length = '$box1_length', box1_width = '$box1_width', box1_height = '$box1_height', box1_weight = '$box1_weight', box2_length = '$box2_length', box2_width = '$box2_width', box2_height = '$box2_height', box2_weight = '$box2_weight', box3_length = '$box3_length', box3_width = '$box3_width', box3_height = '$box3_height', box3_weight = '$box3_weight', box4_length = '$box4_legnth', box4_width = '$box4_width', box4_height = '$box4_height', box4_weight = '$box4_weight', box5_length = '$box5_length', box5_width = '$box5_width', box5_height = '$box5_height', box5_weight = '$box5_weight', box6_length = '$box6_length', box6_width = '$box6_width', box6_height = '$box6_height', box6_weight = '$box6_weight', box7_length = '$box7_length', box7_width = '$box7_width', box7_height = '$box7_height', box7_weight = '$box7_weight', box8_length = '$box8_length', box8_width = '$box8_width', box8_height = '$box8_height', box8_weight = '$box8_weight', box9_length = '$box9_length', box9_width = '$box9_width', box9_height = '$box9_height', box9_weight = '$box9_weight', box10_length = '$box10_length', box10_width = '$box10_width', box10_height = '$box10_height', box10_weight = '$box10_weight', box11_length = '$box11_length', box11_width = '$box11_width', box11_height = '$box11_height', box11_weight = '$box11_weight' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Packages updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update packages!");
			return false;
		}
	}

	public function update_dimensions($code, $item_no, $box1_length, $box1_width, $box1_height, $box2_length, $box2_width, $box2_height, $box3_length, $box3_width, $box3_height, $box4_legnth, $box4_width, $box4_height, $box5_length, $box5_width, $box5_height, $box6_length, $box6_width, $box6_height, $box7_length, $box7_width, $box7_height, $box8_length, $box8_width, $box8_height, $box9_length, $box9_width, $box9_height, $box10_length, $box10_width, $box10_height, $box11_length, $box11_width, $box11_height) {
			$result = $this->db->query("UPDATE packages SET box1_length = '$box1_length', box1_width = '$box1_width', box1_height = '$box1_height', box2_length = '$box2_length', box2_width = '$box2_width', box2_height = '$box2_height', box3_length = '$box3_length', box3_width = '$box3_width', box3_height = '$box3_height', box4_length = '$box4_legnth', box4_width = '$box4_width', box4_height = '$box4_height', box5_length = '$box5_length', box5_width = '$box5_width', box5_height = '$box5_height', box6_length = '$box6_length', box6_width = '$box6_width', box6_height = '$box6_height', box7_length = '$box7_length', box7_width = '$box7_width', box7_height = '$box7_height', box8_length = '$box8_length', box8_width = '$box8_width', box8_height = '$box8_height', box9_length = '$box9_length', box9_width = '$box9_width', box9_height = '$box9_height', box10_length = '$box10_length', box10_width = '$box10_width', box10_height = '$box10_height', box11_length = '$box11_length', box11_width = '$box11_width', box11_height = '$box11_height' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Package dimensions updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update package dimensions!");
			return false;
		}
	}

	public function update_weights($code, $item_no, $box1_weight, $box2_weight, $box3_weight, $box4_weight, $box5_weight, $box6_weight, $box7_weight, $box8_weight, $box9_weight, $box10_weight, $box11_weight) {
		$result = $this->db->query("UPDATE packages SET box1_weight = '$box1_weight', box2_weight = '$box2_weight', box3_weight = '$box3_weight', box4_weight = '$box4_weight', box5_weight = '$box5_weight', box6_weight = '$box6_weight', box7_weight = '$box7_weight', box8_weight = '$box8_weight', box9_weight = '$box9_weight', box10_weight = '$box10_weight', box11_weight = '$box11_weight' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Package weights updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update package weights!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM packages WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM packages WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_dimensions($code, $item_no) {
		static $count = 0;
		$dimensions = array();
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$dimensions = array_merge($dimensions, $this->get_dimensions($code, $item));
			}
			
			$count = 0;
			return $dimensions;
		} else {
			$result = $this->db->query("SELECT * FROM packages WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$box_count = $this->get_box_count($code, $item_no);
				for ($j = 1; $j <= $box_count; $j++) {
					if (!empty($row["box" . $j . "_length"])) array_push($dimensions, $row["box" . $j . "_length"], $row["box" . $j . "_width"], $row["box" . $j . "_height"]);
				}
				$this->output->info("Item: $item_no, Code: $code - Package dimensions found!");
			} else {
				array_push($dimensions, 0, 0, 0);
				$this->output->info("Item: $item_no, Code: $code - Package dimensions not found!");
			}
	
			return $dimensions;
		}
	}

	public function get_weight($code, $item_no) {
		static $count = 0;
		$weights = array();
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$new_array = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$weights = array_merge($weights, $this->get_weight($code, $item));
			}

			$count = 0;
			return $weights;
		} else {
			$result = $this->db->query("SELECT * FROM packages WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$box_count = $this->get_box_count($code, $item_no);
				for ($j = 1; $j <= $box_count; $j++) {
					if (!empty($row["box" . $j . "_weight"])) {
						array_push($weights, $row["box" . $j . "_weight"]);
					} else {
						array_push($weights, 0);
					}
				}
				$this->output->info("Item: $item_no, Code: $code - Package weight found!");
			} else {
				array_push($weights, 0);
				$this->output->info("Item: $item_no, Code: $code - Package weight not found!");
			}

			return $weights;
		}
	}

	public function get_box_count($code, $item_no) {
			static $count = 0;
			$box_count = 0;
			if ($this->set_list->check($code, $item_no) && $count == 0) {
				$count++;
				$total = 0;
				$set = $this->set_list->get_set($code, $item_no);
				for ($i = 0; $i < count($set); $i++) {
					$item = $set[$i];
					$total += $this->get_box_count($code, $item);
				}

				$this->output->info("Item: $item_no, Code: $code - Total Box count: $total!");
				$count = 0;
				return $total;
			} else {
				$result = $this->db->query("SELECT * FROM packages WHERE code = '$code' AND item_no = '$item_no'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					for ($i = 1; $i <= 11; $i++) {
						if (!empty($row["box" . $i . "_length"])) $box_count = $i;
					}
					$this->output->info("Item: $item_no, Code: $code - Box count: $box_count!");
				} else {
					$this->output->info("Item: $item_no, Code: $code - Not found!");
				}
	
				return $box_count;
			}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM packages");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table packages!");
		} else {
			$this->output->error("Failed to get record count in table packages!");
		}

		return $count;
	}
}
?>
