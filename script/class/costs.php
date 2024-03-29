<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("vendors.php");
require_once("set_list.php");
require_once("helper_functions.php");

class costs {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->vendors = new vendors();
		$this->set_list = new set_list();
	}

	public function insert($code, $item_no, $cost, $unit) {
		$result = $this->db->query("INSERT INTO costs (code, item_no, cost, unit, updated_at) VALUES ('$code', '$item_no', '$cost', '$unit',NOW())");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $cost, $unit) {
		$result = $this->db->query("UPDATE costs SET cost = '$cost', unit = '$unit', updated_at = NOW() WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Failed to update!");
			return false;
		}
	}

	public function update_cost($code, $item_no, $cost) {
		$result = $this->db->query("UPDATE costs SET cost = '$cost', updated_at = NOW() WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code, Cost: $cost - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code, Cost: $cost - Failed to update!");
			return false;
		}
	}

	public function update_unit($code, $item_no, $unit) {
		$result = $this->db->query("UPDATE costs SET unit = '$unit', updated_at = NOW() WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code, Unit: $unit - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code, Unit: $unit - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_cost($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		static $count = 0;
		$per_box = $this->vendors->per_box($code);
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$costs = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$cost = $this->get_cost($code, $item);
				array_push($costs, $cost);
			}

			$total = array_sum($costs);
			$total = max($total, $this->get_cost_no_set($code, $item_no));
			$this->output->info("Item: $item_no, code: $code - Total cost $total!");
			$count = 0;
			return $total;
		} else {
			$cost = 0;
			$result = $this->db->query("SELECT cost FROM costs WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$cost = $row['cost']; 
				$unit = $this->get_unit($code, $item_no);
				$cost = $per_box ? $cost : ($cost * $unit);
				$this->output->info("Item: $item_no, code: $code - Cost: $cost!");
			} else {
				$this->output->info("Item: $item_no, code: $code - Cost not found!");
			}

			return $cost;
		}
	}

	public function get_cost_no_set($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		$per_box = $this->vendors->per_box($code);
		$cost = 0;
		$result = $this->db->query("SELECT cost FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$cost = $row['cost']; 
			$unit = $this->get_unit_no_set($code, $item_no);
			$cost = $per_box ? $cost : ($cost * $unit);
			$this->output->info("Item: $item_no, code: $code - Cost: $cost!");
		} else {
			$this->output->info("Item: $item_no, code: $code - Cost not found!");
		}

		return $cost;
	}

	public function get_unit($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		static $count = 0;
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$units = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$unit = $this->get_unit($code, $item);
				array_push($units, $unit);
			}

			$total = array_sum($units);
			$total = max($total, $this->get_unit_no_set($code, $item_no));
			$this->output->info("Item: $item_no, code: $code - Total unit $total!");
			$count = 0;
			return $total;
		} else {
			$unit = 0;
			$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$unit = $row['unit'];
				$this->output->info("Item: $item_no, Code: $code - $unit per box!");
			} else {
				$this->output->info("Item: $item_no, Code: $code - Unit not found!");
			}

			return $unit;
		}
	}

	public function get_unit_no_set($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		$unit = 0;
		$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$unit = $row['unit'];
			$this->output->info("Item: $item_no, Code: $code - $unit per box!");
		} else {
			$this->output->info("Item: $item_no, Code: $code - Unit not found!");
		}

		return $unit;
	}

	public function get_updated_time($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		static $count = 0;
		$updated_time = date('Y-m-d H:i:s', mktime(00, 00, 00, 01, 01, 1970)); 
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$updated_time_array = array();
			$set = $this->set_list->get_set($code, $item_no);
			if (!empty($set)) {
				$item = $set[0];
				for ($i = 0; $i < count($set); $i++) {
					$item = $set[$i];
					$updated_time = $this->get_updated_time($code, $item);
					array_push($updated_time_array, strtotime($updated_time));
				}
			}

			$count = 0;
			if (count($updated_time_array) > 0) {
				$updated_time = date('Y-m-d H:i:s', min($updated_time_array));
			}
			return $updated_time;
		} else {
			$result = $this->db->query("SELECT updated_at FROM costs WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$updated_time = date('Y-m-d H:i:s', strtotime($row['updated_at'])); 
				$this->output->info("Item: $item_no, code: $code - Updated At: $updated_time!");
			} else {
				$this->output->info("Item: $item_no, code: $code - Updated time not found!");
			}

			return $updated_time;
		}
	}

	public function check_exist($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		$result = $this->db->query("SELECT * FROM costs WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM costs");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table costs!");
		} else {
			$this->output->error("Failed to get record count in table costs!");
		}

		return $count;
	}
}

?>
