<?
/* Initialization */
require_once('debugger.php');
require_once("init.php");

class sftp_client {
	private $conn;
	private $server; 
	private $sftp;
	private $files = array();

	public function __construct() {
		$this->output = new debugger;
	}

	public function connect($server) {
		$this->server = $server;
		$this->conn = ssh2_connect($server, 22); 
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
			ssh2_disconnect($this->conn);
			$this->output->info("Disconnected to $this->server!");
			return true;
		} else {
			$this->output->error("Failed to disconnect to $this->server!");
			return false;
		}
	}

	public function login($user, $pass) {
		if ($this->conn) {
			if (ssh2_auth_password($this->conn, $user, $pass)) {
				$this->output->info("Logged in to $this->server!");
				$this->sftp = ssh2_sftp($this->conn);
				if ($this->sftp) {
					return true;
				} else {
					$this->output->error("Failed to initialize SFTP subsystem!");
				}
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

	public function get_sftp() {
		if ($this->conn) {
			return $this->sftp;
		} else {
			$this->output->error("Failed to get connection!");
			return false;
		}
	}

	public function receive($file, $remote_file) {
		if ($this->sftp) {
			$sftp = $this->sftp;
			$stream = @fopen("ssh2.sftp://$sftp$remote_file", 'r');
			if ($stream) {
				$contents = @fread($stream, filesize("ssh2.sftp://$sftp$remote_file"));
				file_put_contents($file, $contents);
				@fclose($stream);
			} else {
				$this->output->error("Failed to open $remote_file!");
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function send($file, $remote_file) {
		if ($this->sftp) {
			$sftp = $this->sftp;
			$stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
			if ($stream) {
				$data_to_send = @file_get_contents($file);
				if ($data_to_send) {
					if (@fwrite($stream, $data_to_send)) {
						$this->output->info("File $file uploaded sucessfully!");
					} else {
						$this->output->error("Could not send data from file: $file!");
					}
					@fclose($stream);
				} else {
					$this->output->error("Failed to open $file!");
				}
			} else {
				$this->output->error("Failed to open $remote_file!");
			}
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}

	public function delete($remote_file) {
		if ($this->sftp) {
			$sftp = $this->sftp;
			unlink("ssh2.sftp://$sftp$remote_file");
			@fclose($stream);
		} else {
			$this->output->notice("Not connected to $this->server!");
			return false;
		}
	}
}
?>
