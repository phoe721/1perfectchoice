<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("ftp_client.php");

class ftp_update {
	private $db;
	private $output;
	private $ftp_client;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->ftp_client = new ftp_client;
	}

	public function add($server, $user, $pass, $directory, $filePath) {
		if ($this->exist($server)) {
			$this->update($server);
		} else {
			$result = $db->query("INSERT INTO ftp_update (server, user, pass, directory, path, update_time) VALUES ('$server', '$user', '$pass', '$directory', '$filePath', NOW())");
			if ($result) {
				$this->output->info("New record added!");
			} else {
				$this->output->error("Failed to insert on database!");
			}
		}
	}

	public function exist($server) {
		$result = $this->db->query("SELECT * FROM ftp_update WHERE server = '$server'");
		if ($result) {
			$this->output->info("Record exists!");
			return true;
		}
		return false;
	}

	public function update() {
		$result = $this->db->query("SELECT * FROM ftp_update GROUP BY server");
		if (mysqli_num_rows($result) == 0) {
			$this->output->info("No records in database!");
		} else {
			while ($row = mysqli_fetch_array($result)) {
				$server = $row['server'];
				$user = $row['user'];
				$pass = $row['pass'];
				$remote_dir = $row['directory'];
				$path = $row['path'];

				if($this->ftp_client->connect($server) && $this->ftp_client->login($user, $pass)) {
					$this->ftp_client->set_passive();
					if (is_dir($row['path'])) {
						$local_dir = array_diff(scandir($row['path']), array('..', '.'));
						foreach ($local_dir as $key => $file) {
							if(is_dir("$file")) {
								// Do nothing
							} else {
								$remote_file = $remote_dir . '/' . $file;
								$local_file = $path . '/' . $file;
								$this->ftp_client->put($remote_file, $local_file);
								$this->output->info("Upload $file successfully!");
							}
						}
					} else {
						$file = $row['path'];
						$remote_file = $remote_dir . '/' . basename($file);
						$this->ftp_client->put($remote_file, $file);
						$this->output->info("Upload $file successfully!");
					}
					$this->ftp_client->disconnect();
				}
			}
		}
	}
}
?>
