<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class weights {
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

	public function insert($code, $item_no, $weight) {
		$result = $this->db->query("INSERT INTO weights (code, item_no, weight) VALUES ('$code', '$item_no', '$wegith')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Weight ($weight) has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $weight) {
		$result = $this->db->query("UPDATE weights SET weight = '$weight' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Weight has been updated to $weight!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to update weight!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM weights WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM weights WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_weight($code, $item_no) {
		if ($this->set_list->check($code, $item_no)) {
			$weights = array();
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$weights = array_merge($weights, $this->get_weight($code, $item));
			}

			return $weights;
		} else {
			$weight = 0;
			$result = $this->db->query("SELECT weight FROM weights WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$weight = $row["weight"];
				$this->output->notice("Item: $item_no, Code: $code - Found weight $weight!");
			} else {
				$this->output->notice("Item: $item_no, Code: $code - Weight not found!");
			}

			return $weight;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM weights");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table weights!");
		} else {
			$this->output->error("Failed to get record count in table weights!");
		}

		return $count;
	}
}

?>
