<?
require_once('init.php');
require_once('database.php');
$db = connect_db();

// Check download directory
remove_outdated_files(DOWNLOAD);
remove_empty_dir(DOWNLOAD);

// Check upload directory
remove_outdated_files(UPLOAD);
remove_empty_dir(UPLOAD);

// Remove Outdated Files
function remove_outdated_files($dir) {
	global $cleanAll, $debug;
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
	$it->rewind();
	while ($it->valid()) {
		if (!$it->isDot()) {
			$filePath = $it->key();
			$fileName = $it->getSubPathName();
			$mtime = filemtime($filePath);
			$diff = time() - $mtime;
			$format = gmdate('H:i:s', $diff);

			if ($debug) {
	        	logger("File name: $fileName ");
				logger("$fileName was last modified: " . date('Y-m-d H:i:s', $mtime));
				logger("Current time: " . date('Y-m-d H:i:s', time()));
				logger("Difference: $diff seconds, which is $format (hour:minute:second)");
			}

			if ($cleanAll) {
				if (unlink($filePath)) logger("$fileName has been deleted");
			} else if ($diff >= ONE_DAY_IN_SECONDS) {
				logger("$fileName is over one day old, going to delete this file");
				if (unlink($filePath)) logger("$fileName has been deleted");
			} 
		}

		$it->next();
	}
}

// Remove Empty Directories
function remove_empty_dir($dir) {
	global $debug;
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD);
	$it->rewind();
	foreach ($it as $file) {
		if (is_dir($file)) {
			if (iterator_count($it->getChildren()) == 0) {
				logger("Empty directory: $file");
				if (rmdir($file)) logger("$file has been removed");
			} else {
				if ($debug) logger("$file is not empty");
			}
		}
	}
}

// Connect to DB
function connect_db() {
	$db	= new database;
	$db->connect("localhost", "root", "c7w2l181", "1perfectchoice");
	mysqli_set_charset($db->getConnection(), "utf8");

	return $db;
}

// Log to logfile
function logger($msg) {
	global $db, $debug;
	$timestring = date('Y-m-d H:i:s', strtotime('now'));
	$result = $db->query("INSERT INTO housekeeping_log (lid, message, datetime) VALUES ('', '$msg', '$timestring')");
}
?>
