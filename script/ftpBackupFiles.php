<?
require_once("class/ftp_client.php");
require_once("class/validator.php");
$ftp_client = new ftp_client();
$remote_dir = "/public_html.1611423940.bak/images/";
$local_dir = "/home/aaron/images/";

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();
$files = $ftp_client->list_files($remote_dir);
foreach($files as $file) {
	if ($file == '.' || $file == '..') {
		continue;
	} else if (preg_match('/AE|BB|CH|GL|HC|HD|HD_WB|HM|HO|IU|KF|LCH|LI|MA|MM|MO|MO_WB|NC|SH|TH|TM|"US"|VIG|ZM/', $file)) {
		$remote_subdir = $remote_dir . $file;
		$ftp_client->change_dir($remote_subdir);
		$local_subdir = $local_dir . $file;
		if (is_dir($local_subdir)) {
			// Do Nothing
		} else {
			if (mkdir($local_subdir, 0755)) {
				printf("$local_subdir directory created!\n");
			} else {
				printf("Failed to create $local_subdir!\n");
				continue;
			}
		}

		$files2 = $ftp_client->list_files($remote_subdir);
		foreach($files2 as $file2) {
			if ($file2 == '.' || $file2 == '..') continue;
			$remote_path = $remote_subdir . "/" . $file2;
			$local_path = $local_subdir . "/" . $file2;
			printf("Downloading $local_path...");
			if ($ftp_client->get($local_path, $file2)) {
				printf("Downloaded\n");
			} else {
				printf("Failed\n");
			}
		}
	}
}

// Disconnect from server
$ftp_client->disconnect();
?>
