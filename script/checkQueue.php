<?
/* ########## NOTES ##########
 * Purpose: 
 * Check queue status
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
check_queue();

// Connect to DB
function connect_db() {
	$db	= new database;
	$con = $db->connect("localhost", "root", "revive", "1perfectchoice");
	mysqli_set_charset($con, "utf8");

	return $db;
}

// Check queue with qid
function check_queue() {
	if (isset($argv[1]) && isset($argv[2])) {
		global $db;
		$uid = $argv[1];
		$qid = $argv[2];
		$output = "";

		$result = $db->query("SELECT status FROM queue WHERE qid = '" . $qid . "'");
		if ($result->num_rows == 0) {
			$output = "No queue found!";	
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$status = $row['status'];

			switch($status) {
				case 0:
					$output = "Queue $qid is not yet processed!";	
					break;
				case 1:
					$output = "Queue $qid is currently processing!";	
					break;
				case 2:
					$output = "Queue $qid has been processed!";	
					break;
				case 3:
					$output = "Queue $qid has failed to process!";	
					break;
			}
		}

		return $output;
	}
}

// Log message 
function logger($msg) {
	$file = fopen(QUEUE_LOG, 'a+');
	if ($file) {
		$timestring = date('Y-m-d h:i:s', strtotime('now'));
		$msg = $timestring . ' - ' . $msg . PHP_EOL;
		fwrite($file, $msg);
	} else {
		fwrite($file, "[ERROR] Unable to open " . QUEUE_LOG . "!");
	}
	fclose($file);
}
?>
