<?
require_once("class/ftp_client.php");
$ftp_client = new ftp_client();
$total = $renameCount = $testrun = 0;
$testrun = 1;
if (isset($argv[1])) {
	// Connect to server
	$ftp_client->connect(FTP_SERVER);
	$ftp_client->login(FTP_USER, FTP_PASS);
	$ftp_client->set_passive();

	$path = $argv[1];
	$files = $ftp_client->list_files($path);
	$total = count($files);
	$ftp_client->change_dir($path);
	foreach($files as $file) {
		if(preg_match('/-[0-9].jpg/', $file)) {
			// Convert -2, -3 to _2, -3 etc. -1 will be removed.
			$new_file = preg_replace('/([0-9A-Z]+)-([0-9].jpg)/','\1_\2', $file);
			$new_file = preg_replace('/(_1)(.jpg)/','\2', $new_file);
			printf("$file is going to rename to $new_file!\n");
			if(!$testrun && $ftp_client->rename($file, $new_file)) {
				printf("$file has been rename to $new_file!\n");
				$renameCount++;
			} else {
				printf("$file cannot be renamed!\n");
			}
		} else {
			//printf("$file is correct, not renamed!\n");
		}

		if(preg_match('/JPG/', $file)) {
			//Convert JPG to jpg
			$new_file = preg_replace('/JPG/','jpg', $file); 
			printf("$file is going to rename to $new_file!\n");
			if(!$testrun && $ftp_client->rename($file, $new_file)) {
				printf("$file has been rename to $new_file!\n");
				$renameCount++;
			} else {
				printf("$file cannot be renamed!\n");
			}
		} else {
			//printf("$file is correct, not renamed!\n");
		}
	}
	
	printf("Total: %d Renamed: %d\n", $total, $renameCount);
} else {
	printf("Usage: php ftpRenameFiles.php path\n");
}

// Disconnect from server
$ftp_client->disconnect();
?>
