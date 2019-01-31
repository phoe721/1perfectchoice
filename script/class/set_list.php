<?
/* Initialization */
require_once("database.php");

class set_list {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no, $item1, $item2, $item3, $item4, $item5, $item6, $item7, $item8, $item9, $item10) {
		if ($this->check($code, $item_no)) {
			$this->output->notice("Item: $item_no, Code: $code exists!");
			return false;
		} else {
			$result = $this->db->query("INSERT INTO set_list (code, item_no, item1, item2, item3, item4, item5, item6, item7, item8, item9, item10) VALUES ('$code', '$item_no', '$item1', '$item2', '$item3', '$item4', '$item5', '$item6', '$item7', '$item8', '$item9', '$item10')");
			if ($result) {
				$this->output->notice("Item: $item_no, Code: $code - Inserted successfully!");
				return true;
			} else {
				$this->output->notice("Item: $item_no, Code: $code - Failed to Insert!");
				return false;
			}
		}
	}
	
	public function update($code, $item_no, $item1, $item2, $item3, $item4, $item5, $item6, $item7, $item8, $item9, $item10) {
		$result = $this->db->query("UPDATE set_list SET item1 = '$item1', item2 = '$item2', item3 = '$item3', item4 = '$item4', item5 = '$item5', item6 = '$item6', item7 = '$item7', item8 = '$item8', item9 = '$item9', item10 = '$item10' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Updated!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM set_list WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check($code, $item_no) {
		$result = $this->db->query("SELECT * FROM set_list WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code - A set!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Not a set!");
			return false;
		}
	}

	public function get_set($code, $item_no) {
		$result = $this->db->query("SELECT * FROM set_list WHERE code = '$code' AND item_no = '$item_no'");
		$set = array();
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			for ($i = 1; $i <= 10; $i++) {
				$count = "item" . $i;
				$item = $row[$count];
				if (!is_null($item) && !empty($item)) array_push($set, $item);
			}
			$set_str = implode(", ", $set);
			$this->output->notice("Item: $item_no, Code: $code - Set: $set_str!");
			return $set;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Not a set!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM set_list");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table set_list!");
		} else {
			$this->output->error("Failed to get record count in table set_list!");
		}

		return $count;
	}
}
?>
