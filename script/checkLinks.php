<?
/* ########## NOTES ##########
 * Purpose: 
 * Check whether links are valid
 * ########################### */

// Initialization
require_once('functions.php');

// Main Program
if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$file = $argv[2];
	process_request($uid, $file);
} else if (isset($_FILES["file"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	$file = $_FILES["file"];
	get_request($uid, $file);
}

// Get request
function get_request($uid, $file) {
	$destination = UPLOAD . $uid . '/' . basename($file["name"]);
	$command = "/usr/bin/php " . __FILE__ . " $uid $destination";

	prepare($uid);
	move_file($uid, $file, $destination);
	create_queue($uid, $command);
}

// Process request
function process_request($uid, $file) {
	prepare($uid);
	check_links($file);
}

// Check validity of links
function check_links($file) {
	global $result_file;
	$total = count(file($file));
	$pass = $fail = $count = 0;
	$output = "";
	$handle = fopen($file, "r");
	if ($handle) {
		while (!feof($handle)){
			$url = trim(fgets($handle));
			if (!empty($url)) { 
				$check = @fopen($url, "r");
				if ($check) {
					$output = "$url\tOK";
					$pass++;
				} else {
					$output = "$url\tFail";
					$fail++;
					log_result($output);
				}
				$count++;
				
				log_status("Pass: $pass Fail: $fail Progress: $count / $total");
			}
		}
	} else {
		log_status("Failed to open $file");
	}
	fclose($handle);

	if ($fail > 0) log_link_file($result_file);
	log_status("Done! Pass: $pass Fail: $fail Total: $count / $total");
}
?>
