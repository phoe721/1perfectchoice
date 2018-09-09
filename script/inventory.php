<?
/* Initialization */
require_once("init.php");
require_once("database.php");
require_once("set_list.php");

class inventory {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->output->debug_on();
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
		$this->set_list = new set_list;
	}

	public function get_inventory($sku) {
		list($code, $item_no) = explode("-", $sku, 2);
		$is_set = $this->set_list->check($code, $item_no);
		if ($is_set) {
			$set = $this->set_list->get_set($code, $item_no);
			$qty_list = array();
			foreach ($set as $item) {
				$result = $this->db->query("SELECT inventory FROM inventory WHERE code = '$code' AND item_no = '$item'");
				if ($result) {
					$row = mysqli_fetch_array($result);
					$qty = $row['inventory'];
					array_push($qty_list, $qty);
					$this->output->info("Item: $item has inventory $qty!");
				} else {
					$this->output->error("Failed to find inventory for $item!");
				}
			}

			$min = min($qty_list);
			$this->output->info("SKU: $sku has inventory $min!");
			return $min;
		} else {
			$qty = -1;
			$result = $this->db->query("SELECT inventory FROM inventory WHERE code = '$code' AND item_no = '$item_no'");
			if ($result) {
				$row = mysqli_fetch_array($result);
				$qty = $row['inventory'];
				$this->output->info("SKU: $sku has inventory $qty!");
			} else {
				$this->output->error("Failed to find inventory for $sku!");
			}

			return $qty;
		}
	}
}
?>
