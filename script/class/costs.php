<?
/* Initialization */
require_once("database.php");
require_once("vendors.php");
require_once("set_list.php");

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
			$this->output->notice("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Inserted successfully!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $cost, $unit) {
		$result = $this->db->query("UPDATE costs SET cost = '$cost', unit = '$unit', updated_at = NOW() WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Updated!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code, Cost: $cost, Unit: $unit - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_cost($code, $item_no) {
		$cost = 0;
		$per_box = $this->vendors->per_box($code);
		$result = $this->db->query("SELECT cost FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$cost = $row['cost']; 
			$unit = $this->get_unit($code, $item_no);
			$cost = $per_box ? $cost : ($cost * $unit);
			$this->output->notice("Item: $item_no, code: $code - Cost: $cost!");
		} else {
			$this->output->notice("Item: $item_no, code: $code - Cost not found!");
		}

		if ($this->set_list->check($code, $item_no)) {
			$costs = array();
			$cost2 = 0;
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$result = $this->db->query("SELECT cost FROM costs WHERE code = '$code' AND item_no = '$item'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$cost2 = $row['cost'];
					$unit2 = $this->get_unit($code, $item);
					$cost2 = $per_box ? $cost2 : ($cost2 * $unit2);
					array_push($costs, $cost2);
					$this->output->notice("Item: $item_no, code: $code - Cost: $cost2!");
				} else {
					$this->output->notice("Item: $item_no, code: $code - Cost not found!");
				}
			}

			// Get MAX(Item Cost, Set Cost)
			$total = max($cost, array_sum($costs));
			$this->output->notice("Item: $item, code: $code - Total cost $total!");
			return $total;
		} else {
			return $cost;
		}
	}

	public function get_unit($code, $item_no) {
		$unit = 0;
		$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$unit = $row['unit'];
			$this->output->notice("Item: $item_no, Code: $code - $unit per box!");
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Unit not found!");
		}

		if ($this->set_list->check($code, $item_no)) {
			$units = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$unit2 = $row['unit'];
					array_push($units, $unit2);
					$this->output->notice("Item: $item, Code: $code - $unit2 per box!");
				} else {
					$this->output->notice("Item: $item, Code: $code - Unit not found!");
				}
			}

			// Get MAX(Item Unit, Set Unit)
			$total = max($unit, array_sum($units));
			$this->output->notice("Item: $item, Code: $code is a set and has $total units!");
			return $total;
		} else {
			return $unit;
		}
	}

	public function get_updated_time($code, $item_no) {
		$updated_time = date('Y-m-d H:i:s', time()); 
		if ($this->set_list->check($code, $item_no)) {
			$costs = array();
			$set = $this->set_list->get_set($code, $item_no);
			$item = $set[0];
			$result = $this->db->query("SELECT updated_at FROM costs WHERE code = '$code' AND item_no = '$item'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$updated_time = date('Y-m-d H:i:s', strtotime($row['updated_at'])); 
				$this->output->notice("Item: $item_no, code: $code - Updated At: $updated_time!");
			} else {
				$this->output->notice("Item: $item_no, code: $code - Updated time not found!");
			}

			return $updated_time;
		} else {
			$result = $this->db->query("SELECT updated_at FROM costs WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$updated_time = date('Y-m-d H:i:s', strtotime($row['updated_at'])); 
				$this->output->notice("Item: $item_no, code: $code - Updated At: $updated_time!");
			} else {
				$this->output->notice("Item: $item_no, code: $code - Updated time not found!");
			}

			return $updated_time;
		}
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM costs");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table costs!");
		} else {
			$this->output->error("Failed to get record count in table costs!");
		}

		return $count;
	}
}

?>
