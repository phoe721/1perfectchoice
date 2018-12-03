<?
/* Initialization */
require_once('debugger.php');

class ftp_client {
	private $conn;
	private $server; 
	private $files = array();

	public function __construct() {
		$this->output = new debugger;
	}

	public function connect($server) {
		$this->server = $server;
		$this->conn = ftp_connect($this->server);
		if ($this->conn) {
			$this->output->notice("Connected to $this->server!");
		} else {
			$this->output->error("Failed to connect to $this->server!");
		}
	}

	public function disconnect() {
		if ($this->conn) {
			ftp_close($this->conn);
			$this->output->notice("Disconnected to $this->server!");
		} else {
			$this->output->error("Failed to disconnect to $this->server!");
		}
	}

	public function login($user, $pass) {
		if ($this->conn) {
			if (@ftp_login($this->conn, $user, $pass)) {
				$this->output->notice("Logged in to $this->server!");
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
			if(ftp_pasv($this->conn, true)) {
				$this->output->notice("Turned passive mode on!");
			} else {
				$this->output->error("Failed to turn passive mode on!");
			}
		}
	}

	public function set_active() {
		if ($this->conn) {
			if(ftp_pasv($this->conn, false)) {
				$this->output->notice("Turned active mode on!");
			} else {
				$this->output->error("Failed to turn active mode on!");
			}
		}
	}

	public function list_files($path) {
		if ($this->conn) {
			$this->files = ftp_nlist($this->conn, $path);
			foreach ($this->files as $file) {
				$this->output->notice($file);
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function pwd() {
		if ($this->conn) {
			$this->output->notice("Current directory: " . ftp_pwd($this->conn) . "!");
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}
	public function change_dir($dir) {
		if ($this->conn) {
			if (ftp_chdir($this->conn, $dir)) {
				$this->output->notice("Changed to directory $dir!");
			} else {
				$this->output->error("Failed to change directory to $dir!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function get($file, $remote_file) {
		if ($this->conn) {
			if (ftp_get($this->conn, $file, $remote_file, FTP_BINARY)) {
				$this->output->notice("Downloaded $remote_file!");
			} else {
				$this->output->error("Failed to download $remote_file!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function put($remote_file, $file) {
		if ($this->conn) {
			if (ftp_put($this->conn, $remote_file, $file, FTP_BINARY)) {
				$this->output->notice("Uploaded $remote_file!");
			} else {
				$this->output->error("Failed to upload $remote_file!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}

	public function rename($file, $new_file) {
		if ($this->conn) {
			if (ftp_rename($this->conn, $file, $new_file)) {
				$this->output->notice("Renamed $file to $new_file!");
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
				$this->output->notice("Deleted $file!");
			} else {
				$this->output->error("Failed to delete $file!");
			}
		} else {
			$this->output->error("Not connected to $this->server!");
		}
	}
}
?>
