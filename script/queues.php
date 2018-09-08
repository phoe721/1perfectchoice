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
require_once('init.php');
require_once('database.php');

class queues {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->output->debug_on();
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	// Check queue with status 0
	public function process_queue() {
		$result = $this->db->query("SELECT qid, command FROM queues WHERE status = 0");
		if ($result->num_rows == 0) {
			//logger("No queue found!");	
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$qid = $row['qid'];
			$command = $row['command'];
	
			// Going to process queue
			update_status($qid, 1);
			$this->output->info("Processing queue $qid");
			shell_exec($command);
	
			// Done Processing
			update_status($qid, 2);
			$this->output->info("Finished processing queue $qid");
		}
	}

	// Update queue status
	public function update_status($qid, $status) {
		$result = $this->db->query("UPDATE queues SET status = '$status', update_time = NOW() WHERE qid = '$qid'");
		if ($result) {
			$this->output->info("Updated queue $qid status to $status");
		} else {
			$this->output->error("Failed to update queue $qid status!");
		}
	}

	// Log queue message 
	public function logger($msg) {
		$timestring = date('Y-m-d H:i:s', strtotime('now'));
		$result = $this->db->query("INSERT INTO queue_log (qid, message, datetime) VALUES ('', '$msg', '$timestring')");
		if ($result) {
			$this->output->info("Successfully Logged $msg to database!");
		} else {
			$this->output->error("Failed to log $msg to database!");
		}
	}
}
?>
