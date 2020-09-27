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
			$this->output->info("Connected to $this->server!");
			return true;
		} else {
			$this->output->error("Failed to connect to $this->server!");
			return false;
		}
	}

	public function disconnect() {
		if ($this->conn) {
			ftp_close($this->conn);
			$this->output->info("Disconnected to $this->server!");
			return true;
		} else {
			$this->output->error("Failed to disconnect to $this->server!");
			return false;
		}
	}

	public function login($user, $pass) {
		if ($this->conn) {
			if (@ftp_login($this->conn, $user, $pass)) {
				$this->output->info("Logged in to $this->server!");
				return true;
			} else {
				$this->output->error("Failed to log in to $this->server!");
				return false;
			}
		}
	}

	public function get_connection() {
		if ($this->conn) {
			return $this->conn;
		} else {
			$this->output->error("Failed to get connection!");
			return false;
		}
	}

	public function set_passive() {
		if ($this->conn) {
			if(ftp_pasv($this->conn, true)) {
				$this->output->info("Turned passive mode on!");
				return true;
			} else {
				$this->output->error("Failed to turn passive mode on!");
				return false;
			}
		}
	}

	public function set_active() {
		if ($this->conn) {
			if(ftp_pasv($this->conn, false)) {
				$this->output->info("Turned active mode on!");
				return true;
			} else {
				$this->output->error("Failed to turn active mode on!");
				return false;
			}
		}
	}

	public function list_files($path) {
		if ($this->conn) {
			$this->files = ftp_nlist($this->conn, $path);
			foreach ($this->files as $file) {
				$this->output->info($file);
			}
			return $this->files;
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function pwd() {
		if ($this->conn) {
			$this->output->info("Current directory: " . ftp_pwd($this->conn) . "!");
			return ftp_pwd($this->conn);
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}
	public function change_dir($dir) {
		if ($this->conn) {
			if (ftp_chdir($this->conn, $dir)) {
				$this->output->info("Changed to directory $dir!");
				return true;
			} else {
				$this->output->error("Failed to change directory to $dir!");
				return false;
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function get($file, $remote_file) {
		if ($this->conn) {
			if (ftp_get($this->conn, $file, $remote_file, FTP_BINARY)) {
				$this->output->info("Downloaded $remote_file!");
				return true;
			} else {
				$this->output->error("Failed to download $remote_file!");
				return false;
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return true;
		}
	}

	public function put($remote_file, $file) {
		if ($this->conn) {
			if (ftp_put($this->conn, $remote_file, $file, FTP_BINARY)) {
				$this->output->info("Uploaded $remote_file!");
				return true;
			} else {
				$this->output->error("Failed to upload $remote_file!");
				return false;
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function rename($file, $new_file) {
		if ($this->conn) {
			if (ftp_rename($this->conn, $file, $new_file)) {
				$this->output->info("Renamed $file to $new_file!");
				return true;
			} else {
				$this->output->error("Failed to rename $file to $new_file!");
				return false;
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function delete($file) {
		if ($this->conn) {
			if (ftp_delete($this->conn, $file)) {
				$this->output->info("Deleted $file!");
				return true;
			} else {
				$this->output->error("Failed to delete $file!");
				return false;
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function size($file) {
		if ($this->conn) {
			if ($size = ftp_size($this->conn, $file)) {
				$this->output->info("$file size is $size!");
				return $size;
			} else {
				$this->output->error("Failed to get size of $file!");
				return false;
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}
}
?>
