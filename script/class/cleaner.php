<?
/* Initialization */
require_once('database.php');

class cleaner {
	private $db;
	private $output;
	private $cleanAll = false;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function remove_outdated_files($dir) {
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
		$it->rewind();
		while ($it->valid()) {
			if (!$it->isDot()) {
				$filePath = $it->key();
				$fileName = $it->getSubPathName();
				$mtime = filemtime($filePath);
				$diff = time() - $mtime;
				$format = gmdate('H:i:s', $diff);
	
	        	$this->output->notice("File name: $fileName");
				$this->output->notice("$fileName was last modified: " . date('Y-m-d H:i:s', $mtime));
				$this->output->notice("Current time: " . date('Y-m-d H:i:s', time()));
				$this->output->notice("Difference: $diff seconds, which is $format (hour:minute:second)");
	
				if ($this->cleanAll) {
					if (unlink($filePath)) $this->output->notice("$fileName has been deleted");
				} else if ($diff >= ONE_DAY_IN_SECONDS) {
					$this->output->notice("$fileName is over one day old, going to delete this file");
					if (unlink($filePath)) $this->output->notice("$fileName has been deleted");
				} 
			}
	
			$it->next();
		}
	}
	
	public function remove_empty_dir($dir) {
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD);
		$it->rewind();
		foreach ($it as $file) {
			if (is_dir($file)) {
				if (iterator_count($it->getChildren()) == 0) {
					$this->output->notice("Empty directory: $file");
					if (rmdir($file)) $this->output->notice("$file has been removed");
				} else {
					$this->output->notice("$file is not empty");
				}
			}
		}
	}

	public function set_clean_all() {
		$this->cleanAll = true;
	}	
}
?>
