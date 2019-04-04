<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class inventory {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->set_list = new set_list;
	}
	
	public function insert($code, $item_no, $qty) {
		$result = $this->db->query("INSERT INTO inventory (code, item_no, qty) VALUES ('$code', '$item_no', '$qty')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inventory ($qty) has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}

	public function update($code, $item_no, $qty) {
		$result = $this->db->query("UPDATE inventory SET qty = '$qty' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inventory has been updated to $qty!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update inventory!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get($code, $item_no) {
		if ($this->set_list->check($code, $item_no)) {
			$qty_list = array();
			$set = $this->set_list->get_set($code, $item_no);
			foreach ($set as $item) {
				$qty = $this->get($code, $item);
				array_push($qty_list, $qty);
			}

			$min = min($qty_list);
			$this->output->info("Item: $item, Code: $code - Inventory has $min for a set!");
			return $min;
		} else {
			$qty = -1;
			$result = $this->db->query("SELECT qty FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$qty = $row['qty'];
				$this->output->info("Item: $item_no, Code: $code - Inventory has $qty!");
			} else {
				$this->output->info("Item: $item_no, Code: $code - Inventory not found!");
			}

			return $qty;
		}
	}
}
?>
