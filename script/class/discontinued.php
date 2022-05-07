<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("set_list.php");

class discontinued {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->set_list = new set_list();
	}

	public function insert($code, $item_no) {
		$result = $this->db->query("INSERT INTO discontinued (code, item_no, updated_at) VALUES ('$code', '$item_no',NOW())");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM discontinued WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		static $count = 0;
		if ($this->set_list->check($code, $item_no) && $count == 0) {
			$count++;
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				if ($this->check($code, $item)) {
					$count = 0;
					return true;
				}
			}
		} else {
			$result = $this->db->query("SELECT * FROM discontinued WHERE code = '$code' AND item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$this->output->info("Item: $item_no, Code: $code - Discontinued!");
				return true;
			} else {
				$this->output->info("Item: $item_no, Code: $code - Active!");
				return false;
			}
		}
	}

	public function get_list() {
		$list = array();
		$result = $this->db->query("SELECT * FROM discontinued");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("List found!");
			while ($row = mysqli_fetch_array($result)) {
				$sku = $row["code"] . "-" . $row["item_no"];
				array_push($list, $sku);
			}
		} else {
			$this->output->notice("There are no records in table discontinued!");
		}

		return $list;
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM discontinued");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table discontinued!");
		} else {
			$this->output->error("Failed to get record count in table discontinued!");
		}

		return $count;
	}
}
?>
