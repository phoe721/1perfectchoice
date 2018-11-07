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
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
		$this->set_list = new set_list;
	}
	
	public function insert($code, $item_no, $qty) {
		$result = $this->db->query("INSERT INTO inventory (code, item_no, qty) VALUES ('$code', '$item_no', '$qty')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code with $qty quantity has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}

	public function update($code, $item_no, $qty) {
		$result = $this->db->query("UPDATE inventory SET qty = '$qty' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code inventory has been updated to $qty!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code inventory failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function get($code, $item) {
		if ($this->set_list->check($code, $item)) {
			$set = $this->set_list->get_set($code, $item);
			$qty_list = array();
			foreach ($set as $item_no) {
				$result = $this->db->query("SELECT qty FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$qty = $row['qty'];
					array_push($qty_list, $qty);
					$this->output->notice("Item: $item_no, Code: $code has inventory $qty!");
				} else {
					$this->output->notice("Item: $item_no, Code: $code inventory not found!");
				}
			}

			$min = min($qty_list);
			$this->output->notice("Item: $item, Code: $code has inventory $min!");
			return $min;
		} else {
			$qty = -1;
			$result = $this->db->query("SELECT qty FROM inventory WHERE code = '$code' AND item_no = '$item'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$qty = $row['qty'];
				$this->output->notice("Item: $item, Code: $code has inventory $qty!");
			} else {
				$this->output->notice("Item: $item, Code: $code inventory not found!");
			}

			return $qty;
		}
	}
}
?>
