<?
/* Initialization */
require_once("Debugger.php");

class cleaner {
	private $output;
	private $cleanAll = true;

	public function __construct() {
		$this->output = new debugger;
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
	
	        	$this->output->info("File name: $fileName");
				$this->output->info("$fileName was last modified: " . date('Y-m-d H:i:s', $mtime));
				$this->output->info("Current time: " . date('Y-m-d H:i:s', time()));
				$this->output->info("Difference: $diff seconds, which is $format (hour:minute:second)");
	
				if ($this->cleanAll) {
					if (unlink($filePath)) $this->output->notice("$fileName has been deleted");
				} else if ($diff >= ONE_DAY_IN_SECONDS) {
					$this->output->info("$fileName is over one day old, going to delete this file");
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
					$this->output->info("Empty directory: $file");
					if (rmdir($file)) $this->output->notice("$file has been removed");
				} else {
					$this->output->info("$file is not empty");
				}
			}
		}
	}

	public function set_clean_all() {
		$this->cleanAll = true;
	}	

	public function set_log_level($level) {
		$this->output->set_log_level($level);
	}
}
?>
