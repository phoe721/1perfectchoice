<?
/* Initialization */
require_once('debugger.php');

class ftp {
	private $conn;
	private $server; 

	public function __construct() {
		$this->output = new debugger;
	}

	public function connect($server) {
		$this->server = $server;
		$this->conn = ftp_connect($this->server);
		if ($this->conn) {
			$this->output->info("Connected to $this->server!");
		} else {
			$this->output->error("Failed to connect to $this->server!");
		}
	}

	public function disconnect() {
		if ($this->conn) {
			ftp_close($this->conn);
			$this->output->info("Connected to $this->server!");
		} else {
			$this->output->error("Failed to disconnect to $this->server!");
		}
	}

	public function login($user, $pass) {
		if ($this->conn) {
			if (@ftp_login($this->conn, $user, $pass)) {
				$this->output->info("Logged in to $this->server!");
			} else {
				$this->output->error("Failed to log in to $this->server!");
			}
		}
	}

	public function get_connection() {
		if ($this->conn) {
			return $this->conn;
		} else {
			$this->output->error("Failed to get connection!");
		}
	}

	public function set_passive() {
		if ($this->conn) {
			if(ftp_pasv($conn, true)) {
				$this->output->info("Turned passive mode on!");
			} else {
				$this->output->error("Failed to turn passive mode on!");
			}
		}
	}

	public function set_active() {
		if ($this->conn) {
			if(ftp_pasv($conn, false)) {
				$this->output->info("Turned active mode on!");
			} else {
				$this->output->error("Failed to turn active mode on!");
			}
		}
	}

	public function list_files($path) {
		if ($this->conn) {
			$files = ftp_nlist($conn, $path);
			return $files;
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function change_dir($dir) {
		if ($this->conn) {
			if (ftp_chdir($this->conn, $dir)) {
				$this->output->info("Changed to directory $dir!");
			} else {
				$this->output->error("Failed to change directory to $dir!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function rename($file, $new_file) {
		if ($this->conn) {
			if (ftp_rename($this->conn, $file, $new_file)) {
				$this->output->info("Renamed $file to $new_file!");
			} else {
				$this->output->error("Failed to rename $file to $new_file!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function delete($file) {
		if ($this->conn) {
			if (ftp_delete($this->conn, $file)) {
				$this->output->info("Deleted $file!");
			} else {
				$this->output->error("Failed to delete $file!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}
}
?>
