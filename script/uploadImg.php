<?
require_once("class/ftp_client.php");
require_once("class/status.php");
$ftp_client = new ftp_client();
$status = new status();

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$status->log_status("Processing $inputFile...");
	$output = fopen($outputFile, "a+");
	if ($output) {
		$fileName = basename($inputFile);
		list($code, $file) = explode("-", $fileName, 2);
		$ftp_client->change_dir("/images/" . $code);
		$ftp_client->put($file, $inputFile);
		$result = "$file uploaded" . PHP_EOL;
		fwrite($output, $result);
	}
	$status->log_status("Done!");
	fclose($output);
}

// Disconnect from server
$ftp_client->disconnect();
?>
