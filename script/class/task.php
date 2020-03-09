<?
/* Initialization */
require_once("database.php");

class task {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($script, $task_name) {
		$result = $this->db->query("INSERT INTO task (script, task_name, sort) VALUES ('$script', '$task_name')");
		if ($result) {
			$this->output->notice("Script: $script, Task Name: $task_name - Inserted successfully!");
			return true;
		} else {
			$this->output->error("Script: $script, Task Name: $task_name - Failed to insert!");
			return false;
		}
	}
	
	public function delete($tid) {
		$result = $this->db->query("DELETE FROM task WHERE tid = '$tid'");
		if ($result) {
			$this->output->notice("Task ID: $tid - Deleted!");
			return true;
		} else {
			$this->output->error("Task ID: $tid - Failed to delete!");
			return false;
		}
	}

	public function get_task_name($tid) {
		$task_name = "";
		$result = $this->db->query("SELECT task_name FROM task WHERE tid = '$tid'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$task_name = $row['task_name'];
			$this->output->notice("Task ID: $tid - Found task $task_name!");
		} else {
			$this->output->warning("Task ID: $tid - task name not found!");
		}

		return $task_name;
	}

	public function get_script($tid) {
		$script = "";
		$result = $this->db->query("SELECT script FROM task WHERE tid = '$tid'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$script = $row['script'];
			$this->output->notice("Task ID: $tid - Found script $script!");
		} else {
			$this->output->warning("Task ID: $tid - script not found!");
		}

		return $script;
	}

	public function get_menu() {
		$list = array(); 
		$result = $this->db->query("SELECT script, task_name FROM task ORDER BY task_name ASC");
		while ($row = mysqli_fetch_array($result)) {
			$script = $row['script'];
			$task_name = $row['task_name'];
			$list[$script] = $task_name;
		}

		return $list;
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM task");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table task!");
		} else {
			$this->output->error("Failed to get record count in table task!");
		}

		return $count;
	}
}
?>
