<?
require_once("debugger.php");

class check_links {
	private $output;

	public function __construct() {
		$this->output = new debugger;
	}

	public function check_link($url) {
		if (!empty($url)) { 
			$check = @fopen($url, "r");
			if ($check) {
				$this->output->notice("URL: $url\tOK");
				return true;
			} else {
				$this->output->error("URL: $url\tFail");
				return false;
			}
		}
	}

	public function check_links_by_file($file) {
		$total = count(file($file));
		$pass = $fail = $count = 0;
		$handle = fopen($file, "r");
		if ($handle) {
			while (!feof($handle)){
				$url = trim(fgets($handle));
				if (!empty($url)) { 
					$check = @fopen($url, "r");
					if ($check) {
						$this->output->notice("URL: $url\tOK");
						$pass++;
					} else {
						$this->output->error("URL: $url\tFail");
						$fail++;
					}
					$count++;
				}
			}
			$this->output->notice("Pass: $pass Fail: $fail Progress: $count / $total");
		} else {
			$this->output->error("Failed to open $file");
		}
		fclose($handle);
	}
}
?>
