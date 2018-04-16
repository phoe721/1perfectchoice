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
$db = connect_db();

// Main Program
process_queue();

// Connect to DB
function connect_db() {
	$db	= new database;
	$con = $db->connect("localhost", "root", "revive", "1perfectchoice");
	mysqli_set_charset($con, "utf8");

	return $db;
}

// Check queue with status 0
function process_queue() {
	global $db;
	$result = $db->query("SELECT qid, command FROM queues WHERE status = 0");
	if ($result->num_rows == 0) {
		//logger("No queue found!");	
	} else {
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$qid = $row['qid'];
		$command = $row['command'];

		// Going to process queue
		update_status($qid, 1);
		logger("Processing queue $qid");
		$output = shell_exec($command);

		// Done Processing
		if (is_null($output)) {
			update_status($qid, 3);
			logger("Failed to process queue $qid");
		} else {
			update_status($qid, 2);
			logger("Finished processing queue $qid");
		}
	}
}

// Update queue status
function update_status($qid, $status) {
	global $db;
	$result = $db->query("UPDATE queues SET status = '$status', update_time = NOW() WHERE qid = '$qid'");
	if ($result) {
		logger("Updated queue $qid status to $status");
	} else {
		logger("Failed to update queue $qid status!");
	}
}

// Log queue message 
function logger($msg) {
	global $db;
	$timestring = date('Y-m-d H:i:s', strtotime('now'));
	$result = $db->query("INSERT INTO queue_log (qid, message, datetime) VALUES ('', '$msg', '$timestring')");
}
?>
