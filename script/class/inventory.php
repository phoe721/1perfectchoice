<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("set_list.php");
require_once("helper_functions.php");

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
		$result = $this->db->query("INSERT INTO inventory (code, item_no, qty, updated_at) VALUES ('$code', '$item_no', '$qty', NOW())");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inventory ($qty) has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}

	public function update($code, $item_no, $qty) {
		$result = $this->db->query("UPDATE inventory SET qty = '$qty', updated_at = NOW() WHERE code = '$code' AND item_no = '$item_no'");
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
		list($code, $item_no) = replace_vendor($code, $item_no);
		$result = $this->db->query("SELECT * FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function check_vendor_in_stock_count($code) {
		$in_stock_count = -1;
		$result = $this->db->query("SELECT count(*) AS count FROM inventory WHERE code = '$code' AND qty > 0");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$in_stock_count = $row['count'];
			$this->output->info("Vendor: $code - in stock count $in_stock_count!");
		} else {
			$this->output->info("Vendor: $code - in stock not found!");
		}

		return $in_stock_count;
	}

	public function check_vendor_item_count($code) {
		$item_count = -1;
		$result = $this->db->query("SELECT count(*) AS count FROM inventory WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$item_count = $row['count'];
			$this->output->info("Vendor: $code - item count $item_count!");
		} else {
			$this->output->info("Vendor: $code - item count not found!");
		}

		return $item_count;
	}

	public function get($code, $item_no) {
		static $count = 0;
		list($code, $item_no) = replace_vendor($code, $item_no);
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$qty_list = array();
			$set = $this->set_list->get_set($code, $item_no);
			foreach ($set as $item) {
				$qty = $this->get($code, $item);
				array_push($qty_list, $qty);
			}

			$min = min($qty_list);
			$this->output->info("Item: $item, Code: $code - Inventory has $min for a set!");
			$count = 0;
			return $min;
		} else {
			$qty = 0;
			$result = $this->db->query("SELECT qty FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$qty = $row['qty'];
				$this->output->info("Item: $item_no, Code: $code - Inventory has $qty!");
			} else {
				$qty = -1;
				$this->output->info("Item: $item_no, Code: $code - Inventory not found!");
			}

			return $qty;
		}
	}

	public function get_updated_time($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		$updated_time = date('Y-m-d H:i:s', mktime(00, 00, 00, 01, 01, 1970)); 
		$result = $this->db->query("SELECT updated_at FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$updated_time = date('Y-m-d H:i:s', strtotime($row['updated_at'])); 
			$this->output->info("Item: $item_no, code: $code - Updated At: $updated_time!");
		} else {
			$this->output->info("Item: $item_no, code: $code - Updated time not found!");
		}

		return $updated_time;
	}

	public function get_vendors() {
		$vendors = array();
		$result = $this->db->query("SELECT DISTINCT code FROM inventory");
		if (mysqli_num_rows($result) > 0) {
			while($row = $result->fetch_array()) {
				$vendors[] = $row['code'];
			}
			return $vendors;
		} else {
			$this->output->info("Vendors not found!");
			return false;
		}
	}

	public function truncate() {
		$result = $this->db->query("TRUNCATE TABLE inventory");
		if ($result) {
			$this->output->info("Inventory table has been truncated!");
			return true;
		} else {
			$this->output->info("Failed to truncate inventory table!");
			return false;
		}
	}
}
?>
