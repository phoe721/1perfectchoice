<?
require_once("class/ftp_client.php");
require_once("class/status.php");
require_once("class/validator.php");
$ftp_client = new ftp_client();
$status = new status();
$validator = new validator();
$local_dir = "/home/aaron/images/";
$total = $copyCount = 0;

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
} else if (isset($argv[1]) && isset($argv[2])) {
	$src_folder = $argv[1];
	$dest_folder = $argv[2];
	$tmp_folder = "/tmp/";
	$files = $ftp_client->list_files($src_folder);
	$total = count($files);
	$ftp_client->change_dir($src_folder);
	foreach($files as $src_file) {
		$file = basename($src_file);
		if(preg_match('/^05/', $file)) {
			$new_file = preg_replace('/^05/','', $file);
			$dest_file = $dest_folder . $new_file;
			$tmp_file = $tmp_folder . $file;
			if ($ftp_client->size($dest_file)) {
				printf("File exists - $file!\n");
			} else {
				printf("$src_file is going to copy to $dest_file!\n");
				/*
				if($ftp_client->copy($src_file, $dest_file, $tmp_file)) {
					printf("$src_file has been copied to $dest_file!\n");
					$copyCount++;
				} else {
					printf("$src_file cannot be copied!\n");
				}
				 */
			}
		}
	}
	printf("Total: %d copied: %d\n", $total, $copyCount);
}

// Disconnect from server
$ftp_client->disconnect();
?>
