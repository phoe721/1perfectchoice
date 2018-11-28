<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class costs {
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

	public function insert($code, $item_no, $cost, $unit) {
		$result = $this->db->query("INSERT INTO costs (code, item_no, cost, unit, updated_at) VALUES ('$code', '$item_no', '$cost', '$unit',NOW())");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code with $cost per unit and $unit per box has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update($code, $item_no, $cost, $unit) {
		$result = $this->db->query("UPDATE costs SET cost = '$cost', unit = '$unit', updated_at = NOW() WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has updated cost to $cost with $unit per box!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to update cost and unit!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function get_cost($code, $item_no) {
		$cost = 0;
		$result = $this->db->query("SELECT cost FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$cost = $row['cost'];
			$this->output->notice("Item: $item_no, code: $code costs $cost!");
		} else {
			$this->output->notice("Item: $item_no, code: $code cost not found!");
		}

		if ($this->set_list->check($code, $item_no)) {
			$costs = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$result = $this->db->query("SELECT cost FROM costs WHERE code = '$code' AND item_no = '$item'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$cost = $row['cost'];
					array_push($costs, $cost);
					$this->output->notice("Item: $item_no, code: $code costs $cost!");
				} else {
					$this->output->notice("Item: $item_no, code: $code cost not found!");
				}
			}


			// Get MAX(Item Cost, Set Cost)
			$total = max($cost, array_sum($costs));
			$this->output->notice("Item: $item, code: $code has total costs $total!");
			return $total;
		} else {
			return $cost;
		}
	}

	public function get_unit($code, $item_no) {
		$unit = 0;
		if ($this->set_list->check($code, $item_no)) {
			$units = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$unit = $row['unit'];
					array_push($units, $unit);
					$this->output->notice("Item: $item, Code: $code has $unit unit per box!");
				} else {
					$this->output->notice("Item: $item, Code: $code unit per box not found!");
				}
			}
			$this->output->notice("Item: $item, Code: $code is a set!");

			return 1;
		} else {
			$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$unit = $row['unit'];
				$this->output->notice("Item: $item_no, Code: $code has $unit unit per box!");
			} else {
				$this->output->notice("Item: $item_no, Code: $code unit per box not found!");
			}

			return $unit;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM costs");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table costs!");
		} else {
			$this->output->error("Failed to get record count in table costs!");
		}

		return $count;
	}
}

?>
