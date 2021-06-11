<?
require_once("class/ftp_client.php");
require_once("class/status.php");
require_once("class/validator.php");
$ftp_client = new ftp_client();
$status = new status();
$validator = new validator();
$local_dir = "/home/aaron/images/";

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$line = trim(fgets($input));
			if (!empty($line)) {
				list($src, $dest) = explode("\t", $line);
				$ftp_client->get($path, $url);
				$result = "$src $dest" . PHP_EOL;
				fwrite($output, $result);
			}
		}
	}
	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}

// Disconnect from server
$ftp_client->disconnect();
?>
