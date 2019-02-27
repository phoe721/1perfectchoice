<?
/* Initialization */
require_once("database.php");

class UPC {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $upc) {
		$result = $this->db->query("INSERT INTO UPC (code, item_no, upc) VALUES ('$code', '$item_no', '$upc')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code, UPC: $upc  - Inserted successfully!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code, UPC: $upc  - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $upc) {
		$result = $this->db->query("UPDATE UPC SET upc = '$upc' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Updated to $upc!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to update UPC code!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM UPC WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_upc($code, $item) {
		$upc = '';
		$result = $this->db->query("SELECT upc FROM UPC WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$upc = $row['upc'];
			$this->output->notice("Item: $item, Code: $code - UPC: $upc found!");
		} else {
			$this->output->notice("Item: $item, Code: $code - UPC not found!");
		}

		return $upc;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM UPC WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM UPC");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table UPC!");
		} else {
			$this->output->error("Failed to get record count in table UPC!");
		}

		return $count;
	}
}

?>
