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
			$this->output->notice("Item: $item_no, Code: $code, ASIN: $asin - Inserted successfully!");
			return true;
		} else {
			$this->output->error("Item: $item_no, Code: $code, ASIN: $asin - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $asin) {
		$result = $this->db->query("UPDATE ASIN SET asin = '$asin' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Updated ASIN to $asin!");
			return true;
		} else {
			$this->output->error("Item: $item_no, Code: $code - Failed to update ASIN!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM ASIN WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->error("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_asin($code, $item) {
		$asin = "";
		$result = $this->db->query("SELECT asin FROM ASIN WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$asin = $row['asin'];
			$this->output->notice("Item: $item, code: $code - Found ASIN $asin!");
		} else {
			$this->output->warning("Item: $item, code: $code - ASIN not found!");
		}

		return $asin;
	}

	public function get_sku($asin) {
		$sku = "";
		$result = $this->db->query("SELECT code, item_no FROM ASIN WHERE asin = '$asin'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$sku = $row['code'] . "-" . $row['item_no'];
			$this->output->notice("ASIN: $asin - Found SKU $sku!");
		} else {
			$this->output->warning("ASIN: $asin - SKU not found!");
		}

		return $sku;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM ASIN WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM ASIN");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table ASIN!");
		} else {
			$this->output->error("Failed to get record count in table ASIN!");
		}

		return $count;
	}
}
?>
