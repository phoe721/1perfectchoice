<?
/* Initialization */
require_once("debugger.php");

class keywords {
	private $output;

	public function __construct() {
		$this->output = new debugger;
	}

	public function get_keywords($str) {
		$str = strtolower(trim($str));
		$str = $this->filter($str);
		$str = $this->filter_misspelled_words($str);
		$str = $this->filter_bad_keywords($str);	
		$this->output->info("Keywords: $str");

		return $str;
	}

	public function get_keywords_by_file($file) {
		$handle = fopen($file, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$keywords = $this->get_keywords($line);
			}
		} else {
			$this->output->error("Failed to open $file");
		}
		fclose($handle);
	}

	public function filter_misspelled_words($str) {
		// Check if it's valid English word
		$pspell_link = pspell_new("en");
		$pieces = explode(' ', $str);
		for ($i = 0; $i < count($pieces); $i++) {
			if (!pspell_check($pspell_link, $pieces[$i])) {
				unset($pieces[$i]);
			}
		}

		$pieces = array_unique($pieces);
		$str = implode(' ', $pieces);
		return $str;
	}

	public function filter_bad_keywords($str) {
		$old = explode(' ', $str);
		$bad_keywords = array('the','of','with','set','by','only','in','and','up','or','a','is','be','lot','it','which');
		$new = array_diff($old, $bad_keywords);
		$str = implode(' ', $new);

		return $str;
	}
	
	public function filter($str) {
		$str = preg_replace('/[^a-z]+/i', ' ', $str);
		$str = preg_replace('/\s\s+/', ' ', $str);	// Remove extra spaces
		$str = trim($str);
	
		return $str;
	}
}
?>
