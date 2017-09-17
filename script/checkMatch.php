<?
/* ########## NOTES ##########
 * Purpose: 
 * Check whether matches are found in file 1 against file 2.
 * ########################### */

// Initialization
require_once('functions.php');

// Main Program
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$uid = $argv[1];
	$file1 = $argv[2];
	$file2 = $argv[3];
	process_request($uid, $file1, $file2);
} else if(isset($_FILES["file1"]) && isset($_FILES["file2"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	$file1 = $_FILES["file1"];
	$file2 = $_FILES["file2"];
	get_request($uid, $file1, $file2);
}

// Get request
function get_request($uid, $file1, $file2) {
	prepare($uid);	// Prepare directory
	$destination1 = UPLOAD . $uid . '/' . basename($file1["name"]);
	$destination2 = UPLOAD . $uid . '/' . basename($file2["name"]);
	$command = "/usr/bin/php " . __FILE__ . " $uid $destination1 $destination2";

	move_file($uid, $file1, $destination1);
	move_file($uid, $file2, $destination2);
	create_queue($uid, $command);
}

// Process request
function process_request($uid, $file1, $file2) {
	prepare($uid);
	check_match($file1, $file2);
}

// Check Match
function check_match($file1, $file2) {
	global $result_file;
	$f1 = fopen($file1, "r");
	$f2 = fopen($file2, "r");

	// Put file2 data into array
	if ($f2) {
		while(!feof($f2)) {
			$data = fgets($f2);
			$data = trim($data);
			array_push($match, $data);
		}
	}
	fclose($f2);

	// Check input file for match
	if ($f1) {
		while (!feof($f1)){
			$line = trim(fgets($f1));
			$found = check_match_by_line($line);
			$output = $line . "\t" . $found; 
			log_result($output);
		}
	} else {
		logger("Failed to open $file1");
	}
	fclose($f1);

	log_link_file($result_file);
	log_status("Done!");
}

function check_match_by_line($line) {
	global $match;
	foreach ($match as $value) {
		if (preg_match('/' . $value . '/i', $line)) {
			return $value;
		}
	}

	return "N/A";
}

?>
