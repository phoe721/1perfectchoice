<?
/* Initialization */
require_once('functions.php');

$result = array();
if (isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);

	// Push links to result
	if (file_exists($link_file)) {
		$result['link'] = array();
		$handle = fopen($link_file, "r");
		if ($handle) {
			while (!feof($handle)){
				$url = trim(fgets($handle));
				if (!empty($url)) array_push($result['link'], $url);
			}
		} else {
			$result['status'] = "Failed to open $link_file";
		}
		fclose($handle);
	}

	// Push output to result
	if (file_exists($result_file)) {
		$result['output'] = array();
		$handle = fopen($result_file, "r");
		if ($handle) {
			while (!feof($handle)){
				$line = fgets($handle);
				if (!empty($line)) array_push($result['output'], $line);
			}
		} else {
			$result['status'] = "Failed to open $result_file";
		}
		fclose($handle);
	}

	// Output status
	if (file_exists($status_file)) {
		$file = fopen($status_file, 'r') or die('Unable to open file!');
		if ($file) {
			$result['status'] = trim(fgets($file));
		} else {
			$result['status'] = "Unable to open status file!";
		}
		fclose($file);

		echo json_encode($result);
	}
}
?>
