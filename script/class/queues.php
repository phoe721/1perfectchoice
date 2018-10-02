<?
/* ########## NOTES ##########
 * Purpose: 
 * Process 1 queue every 5 minute
 * 
 * Status Code:
 * Status 0 - Not processed
 * Status 1 - Processing
 * Status 2 - Processed
 * Status 3 - Failed
 * ########################### */

// Initialization
require_once('database.php');

class queues {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function create_queue($UID, $command) {
		$result = $this->db->query("INSERT INTO queues (UID, command, status, insert_time, update_time) VALUES ('$UID', '$command', '0', NOW(), NOW())");
		if ($result) {
			$last_id = $this->db->last_insert_id();
			$this->output->notice("Queue created, queue number is $last_id!");
		} else {
			$last_id = null;
			$this->output->error("Failed to create queue!");
		}

		return $last_id;
	}

	public function process_queue() {
		$result = $this->db->query("SELECT qid, command FROM queues WHERE status = 0");
		if ($result->num_rows == 0) {
			$this->output->info("No queue found!");	
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$qid = $row['qid'];
			$command = $row['command'];
	
			// Going to process queue
			$this->update_status($qid, 1);
			$this->output->notice("Processing queue $qid");
			shell_exec($command);
	
			// Done Processing
			$this->update_status($qid, 2);
			$this->output->notice("Finished processing queue $qid");
		}
	}

	// Update queue status
	public function update_status($qid, $status) {
		$result = $this->db->query("UPDATE queues SET status = '$status', update_time = NOW() WHERE qid = '$qid'");
		if ($result) {
			$this->output->notice("Updated queue $qid status to $status");
		} else {
			$this->output->error("Failed to update queue $qid status!");
		}
	}

	// Get Queue ID Using UID
	public function get_qid($UID) {
		$result = $this->db->query("SELECT qid FROM queues WHERE UID = '" . $UID . "'");
		if ($result->num_rows == 0) {
			$this->output->warning("No queue ID found!");	
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$qid = $row['qid'];
			$this->output->notice("Queue ID found: $qid");
			return $qid;
		}
	}
}
?>
