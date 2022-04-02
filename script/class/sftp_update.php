<?
/* Initialization */
require_once("database.php");
require_once("sftp_client.php");

class sftp_update {
	private $db;
	private $output;
	private $sftp_client;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->sftp_client = new sftp_client;
	}

	public function add($server, $user, $pass, $directory, $filePath) {
		if ($this->exist($server)) {
			$this->update($server);
		} else {
			$result = $db->query("INSERT INTO sftp_update (server, user, pass, directory, path, update_time) VALUES ('$server', '$user', '$pass', '$directory', '$filePath', NOW())");
			if ($result) {
				$this->output->info("New record added!");
			} else {
				$this->output->error("Failed to insert on database!");
			}
		}
	}

	public function exist($server) {
		$result = $this->db->query("SELECT * FROM sftp_update WHERE server = '$server'");
		if ($result) {
			$this->output->info("Record exists!");
			return true;
		}
		return false;
	}

	public function update() {
		$result = $this->db->query("SELECT * FROM sftp_update GROUP BY server");
		if (mysqli_num_rows($result) == 0) {
			$this->output->info("No records in database!");
		} else {
			while ($row = mysqli_fetch_array($result)) {
				$server = $row['server'];
				$user = $row['user'];
				$pass = $row['pass'];
				$remote_dir = $row['directory'];
				$path = $row['path'];

				if($this->sftp_client->connect($server) && $this->sftp_client->login($user, $pass)) {
					$file = $row['path'];
					$this->output->info("$file");
					$remote_file = '/' . $remote_dir . '/' . basename($file);
					$this->output->info("$remote_file");
					$this->sftp_client->send($file, $remote_file);
					$this->sftp_client->disconnect();
				}
			}
		}
	}
}
?>
