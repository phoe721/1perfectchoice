<?
/* Initialization */
require_once("database.php");

class ASIN {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no, $asin) {
		$result = $this->db->query("INSERT INTO ASIN (code, item_no, asin) VALUES ('$code', '$item_no', '$asin')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code with $asin has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update($code, $item_no, $asin) {
		$result = $this->db->query("UPDATE ASIN SET asin = '$asin' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has updated ASIN to $asin!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to update ASIN!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM ASIN WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function get_asin($code, $item) {
		$asin = false;
		$result = $this->db->query("SELECT asin FROM ASIN WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$asin = $row['asin'];
			$this->output->notice("Item: $item, code: $code ASIN $asin!");
		} else {
			$this->output->notice("Item: $item, code: $code ASIN not found!");
		}

		return $asin;
	}

	public function get_sku($asin) {
		$sku = false;
		$result = $this->db->query("SELECT code, item_no FROM ASIN WHERE asin = '$asin'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$code = $row['code'];
			$item_no = $row['item_no'];
			$sku = "$code-$item_no";
			$this->output->notice("ASIN $asin SKU $sku!");
		} else {
			$this->output->notice("ASIN $asin SKU not found!");
		}

		return $sku;
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM ASIN");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table ASIN!");
		} else {
			$this->output->error("Failed to get record count in table ASIN!");
		}

		return $count;
	}
}

?>
