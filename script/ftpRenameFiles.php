<?
require_once("class/ftp_client.php");
require_once("class/status.php");
require_once("class/validator.php");
$ftp_client = new ftp_client();
$status = new status();
$validator = new validator();
$total = $renameCount = $testrun = 0;
$testrun = 1;
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	// Connect to server
	$ftp_client->connect(FTP_SERVER);
	$ftp_client->login(FTP_USER, FTP_PASS);
	$ftp_client->set_passive();
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
				list($path, $old_name, $new_name) = explode("\t", $line);
				$ftp_client->change_dir($path);
				if($ftp_client->rename($old_name, $new_name)) {
					$result = "$old_name has been renamed to $new_name!" . PHP_EOL;
				} else {
					$result = "$file cannot be renamed!" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
} else if (isset($argv[1])) {
	// Connect to server
	$ftp_client->connect(FTP_SERVER);
	$ftp_client->login(FTP_USER, FTP_PASS);
	$ftp_client->set_passive();

	$path = $argv[1];
	$files = $ftp_client->list_files($path);
	$total = count($files);
	$ftp_client->change_dir($path);
	foreach($files as $file) {
		// Convert -2, -3 to _2, -3 etc. -1 will be removed.
		/*
		if(preg_match('/-[0-9].jpg/', $file)) {
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
		 */

		// SR will be removed.
		if(preg_match('/PDEX-F/', $file)) {
			$new_file = preg_replace('/PDEX-F/','01', $file);
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

		//Convert JPG to jpg
		/*
		if(preg_match('/JPG/', $file)) {
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
		 */
	}
	
	printf("Total: %d Renamed: %d\n", $total, $renameCount);
} else {
	printf("Usage: php ftpRenameFiles.php path\n");
}

// Disconnect from server
$ftp_client->disconnect();
?>
