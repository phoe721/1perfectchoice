<?
/* Initialization */
require_once("database.php");
require_once("init.php");

class UPC {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->output->set_log_level(4);
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $upc) {
		$result = $this->db->query("INSERT INTO UPC (code, item_no, upc) VALUES ('$code', '$item_no', '$upc')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code, UPC: $upc  - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code, UPC: $upc  - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $upc) {
		$result = $this->db->query("UPDATE UPC SET upc = '$upc' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Updated to $upc!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update UPC code!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM UPC WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_sku($upc) {
		$sku = "";
		$result = $this->db->query("SELECT code, item_no FROM UPC WHERE upc = '$upc'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$sku = $row['code'] . "-" . $row['item_no'];
			$this->output->info("UPC: $upc - Found SKU $sku!");
		} else {
			$this->output->info("UPC: $upc - SKU not found!");
		}

		return $sku;
	}

	public function get_upc($code, $item) {
		$upc = '';
		$result = $this->db->query("SELECT upc FROM UPC WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$upc = $row['upc'];
			$this->output->info("Item: $item, Code: $code - UPC: $upc found!");
		} else {
			$this->output->info("Item: $item, Code: $code - UPC not found!");
		}

		return $upc;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM UPC WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM UPC");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table UPC!");
		} else {
			$this->output->error("Failed to get record count in table UPC!");
		}

		return $count;
	}
}

?>
