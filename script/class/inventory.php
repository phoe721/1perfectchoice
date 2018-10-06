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

	public function get_inventory($code, $item) {
		if ($this->set_list->check($code, $item)) {
			$set = $this->set_list->get_set($code, $item);
			$qty_list = array();
			foreach ($set as $item_no) {
				$result = $this->db->query("SELECT qty FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$qty = $row['qty'];
					array_push($qty_list, $qty);
					$this->output->notice("Item: $item_no, code: $code has inventory $qty!");
				} else {
					$this->output->notice("Item: $item_no, code: $code inventory not found!");
				}
			}

			$min = min($qty_list);
			$this->output->notice("Item: $item, code: $code has inventory $min!");
			return $min;
		} else {
			$qty = -1;
			$result = $this->db->query("SELECT qty FROM inventory WHERE code = '$code' AND item_no = '$item'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$qty = $row['qty'];
				$this->output->notice("Item: $item, code: $code has inventory $qty!");
			} else {
				$this->output->notice("Item: $item, code: $code inventory not found!");
			}

			return $qty;
		}
	}
}
?>
