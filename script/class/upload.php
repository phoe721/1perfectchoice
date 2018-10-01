<?
// Initialization
require_once("debugger.php");

class upload{
	private $output;
	private $uid;
	private $file;
	private $fileName;
	private $sourceFile;
	private $error;
	private $size;
	private $targetDir;
	private $targetFile;

	public function __construct() {
		$this->output = new debugger;
		$this->output->debug_on();
	}

	public function set_UID($uid) {
		$this->uid = $uid;
	}

	public function set_file($file) {
		$this->file = $file;
		$this->fileName = $file['name'];
		$this->sourceFile = $file['tmp_name'];
		$this->error = $file['error'];
		$this->size = $file['size'];
		$this->targetDir = UPLOAD . $this->uid . '/';
		if (!is_dir($this->targetDir)) mkdir($this->targetDir, 0777, true);
	}

	public function get_filename() {
		return $this->fileName;
	}

	public function get_file_ext() {
		$ext = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
		return $ext;
	}

	public function get_sourceFile() {
		return $this->sourceFile;
	}

	public function get_error() {
		switch($this->error) {
			case UPLOAD_ERR_OK:
				$this->targetFile = $this->targetDir . basename($this->fileName);
				if (file_exists($this->targetFile)) {
					$response = "File already exists!";
				} else {
					if ($this->size > MAX_UPLOAD_SIZE) {
						$response = "File size too large!";
					} else {
						if (move_uploaded_file($this->sourceFile, $this->targetFile)) { 
							$response = "File uploaded!";
						} else {
							$response = "File upload fail!";
						}
					}
				}
				break;
			case UPLOAD_ERR_INI_SIZE: 
				$response = "File size exceeds maximum file size specificed in php.ini!";
				break;
			case UPLOAD_ERR_FORM_SIZE: 
				$response = "File size exceeds maximum file size specificed in HTML form!";
				break;
			case UPLOAD_ERR_PARTIAL:
				$response = "File only partially uploaded!";
				break;
			case UPLOAD_ERR_NO_FILE:
				$response = "No file was uploaded!";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$response = "Missing temporary folder!";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$response = "Failed to write file to disk!";
				break;
			case UPLOAD_ERR_EXTENSION:
				$response = "PHP extension stopped the file upload!";
				break;
			default:
				$response = 'Unknown error';
		}

		return $response;
	}

	public function get_size() {
		$this->output->info("file size: $this->size");
		return $this->size;
	}

	public function set_targetDir($dir) {
		$this->targetDir = $dir;
	}

	public function get_targetDir() {
		return $this->targetDir;
	}
}
?>
