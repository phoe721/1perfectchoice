<?
/* Initialization */
require_once("database.php");

class UPC {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no, $upc) {
		$result = $this->db->query("INSERT INTO UPC (code, item_no, upc) VALUES ('$code', '$item_no', '$upc')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update($code, $item_no, $upc) {
		$result = $this->db->query("UPDATE UPC SET upc = '$upc' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been updated!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to update product!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM UPC WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function get_upc($code, $item) {
		$upc = '';
		$result = $this->db->query("SELECT upc FROM UPC WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$upc = $row['upc'];
			$this->output->notice("Item: $item, code: $code product UPC found!");
		} else {
			$this->output->notice("Item: $item, code: $code product UPC not found!");
		}

		return $upc;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM UPC WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code exists!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code not exist!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM UPC");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table product!");
		} else {
			$this->output->error("Failed to get record count in table product!");
		}

		return $count;
	}
}

?>
