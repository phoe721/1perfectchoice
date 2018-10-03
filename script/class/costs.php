<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class costs {
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

	public function insert($code, $item_no, $cost, $unit) {
		$result = $this->db->query("INSERT INTO costs (code, item_no, cost, unit) VALUES ('$code', '$item_no', '$cost', '$unit')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code with $cost per unit and $unit per box has been inserted successfully!");
			return true;
		} else {
			$this->output->error("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update_cost($code, $item_no, $cost) {
		$result = $this->db->query("UPDATE costs SET cost = '$cost' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code has updated cost to $cost!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code failed to update cost!");
			return false;
		}
	}

	public function update_unit($code, $item_no, $unit) {
		$result = $this->db->query("UPDATE costs SET unit = '$unit' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code has updated unit per box to $unit!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code failed to update unit per box!");
			return false;
		}
	}

	public function get_cost($code, $item_no) {
		$cost = 0;
		$total = 0;
		if ($this->set_list->check($code, $item_no)) {
			$set = $this->set_list->get_set($code, $item_no);
			for ($i = 0; $i < count($set); $i++) {
				$item_no = $set[$i];
				$result = $this->db->query("select cost from costs where code = '$code' and item_no = '$item_no'");
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					$cost = $row['cost'];
					$total += $cost;
					$this->output->info("item: $item_no, code: $code costs $cost!");
				} else {
					$this->output->info("item: $item_no, code: $code cost not found!");
				}
			}

			return $total;
		} else {
			$result = $this->db->query("select cost from costs where code = '$code' and item_no = '$item_no'");
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				$cost = $row['cost'];
				$this->output->info("item: $item_no, code: $code costs $cost!");
			} else {
				$this->output->info("item: $item_no, code: $code cost not found!");
			}

			return $cost;
		}
	}

	public function get_unit($code, $item_no) {
		$unit = -1;
		$result = $this->db->query("SELECT unit FROM costs WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$unit = $row['unit'];
			$this->output->info("Item: $item_no, Code: $code has $unit unit per box!");
		} else {
			$this->output->info("Item: $item_no, Code: $code unit per box not found!");
		}
		return $unit;
	}

	public function truncate_table() {
		$result = $this->db->query("TRUNCATE TABLE costs");
		if ($result) {
			$this->output->info("Table truncated!");
			return true;
		} else {
			$this->output->error("Failed to truncate!");
			return false;
		}
	}
	
	public function update_by_file($filePath) {
		$result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE costs");
		if ($result) {
			$this->output->info("Table updated with $filePath!");
			return true;
		} else {
			$this->output->error("Failed to update table with $filePath!");
			return false;
		}
	}
	
	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM costs");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->info("There are $count records in table costs!");
		} else {
			$this->output->error("Failed to get record count in table costs!");
		}

		return $count;
	}
}

?>
