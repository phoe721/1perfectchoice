<?
// Initialization
require_once(__DIR__ . "/../init.php");
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
	private $outputDir;
	private $outputFile;
	private $statusFile;

	public function __construct() {
		$this->output = new debugger();
	}

	public function set_UID($uid) {
		$this->uid = $uid;
		$this->outputDir = DOWNLOAD . $this->uid . "/";
		if (!is_dir($this->outputDir)) mkdir($this->outputDir, 0777, true);
		$this->outputFile = $this->outputDir . "result.txt";
		$this->statusFile = $this->outputDir . "status.txt";
	}

	public function get_UID() {
		return $this->uid;
	}

	public function set_file($file) {
		$this->file = $file;
		$this->fileName = $file["name"];
		$this->sourceFile = $file["tmp_name"];
		$this->error = $file["error"];
		$this->size = $file["size"];
		$this->targetDir = UPLOAD . $this->uid . "/";
		if (!is_dir($this->targetDir)) mkdir($this->targetDir, 0777, true);
		$this->targetFile = $this->targetDir . basename($this->fileName);
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
				if (file_exists($this->targetFile)) {
					$this->output->notice("File already exists!");
					$response = "File already exists!";
				} else {
					if ($this->size > MAX_UPLOAD_SIZE) {
						$this->output->notice("File size too large!");
						$response = "File size too large!";
					} else {
						if (move_uploaded_file($this->sourceFile, $this->targetFile)) { 
							$this->output->notice("File uploaded!");
							$response = "File uploaded!";
						} else {
							$this->output->notice("File upload fail!");
							$response = "File upload fail!";
						}
					}
				}
				break;
			case UPLOAD_ERR_INI_SIZE: 
				$this->output->notice("File size exceeds maximum file size specificed in php.ini!");
				$response = "File size exceeds maximum file size specificed in php.ini!";
				break;
			case UPLOAD_ERR_FORM_SIZE: 
				$this->output->notice("File size exceeds maximum file size specificed in HTML form!");
				$response = "File size exceeds maximum file size specificed in HTML form!";
				break;
			case UPLOAD_ERR_PARTIAL:
				$this->output->notice("File only partially uploaded!");
				$response = "File only partially uploaded!";
				break;
			case UPLOAD_ERR_NO_FILE:
				$this->output->notice("No file was uploaded!");
				$response = "No file was uploaded!";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->output->notice("Missing temporary folder!");
				$response = "Missing temporary folder!";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$this->output->notice("Failed to write file to disk!");
				$response = "Failed to write file to disk!";
				break;
			case UPLOAD_ERR_EXTENSION:
				$this->output->notice("PHP extension stopped the file upload!");
				$response = "PHP extension stopped the file upload!";
				break;
			default:
				$this->output->notice("Unknown error");
				$response = "Unknown error";
		}

		return $response;
	}

	public function get_size() {
		return $this->size;
	}

	public function get_targetFile() {
		return $this->targetFile;
	}

	public function get_outputFile() {
		return $this->outputFile;
	}

	public function get_statusFile() {
		return $this->statusFile;
	}
}
?>
