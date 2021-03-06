<?
require_once("class/ftp_client.php");
$ftp_client = new ftp_client();
$remote_dir = "/public_html.1611423940.bak/images/";
$local_dir = "/home/aaron/images/";

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();
$file_list = $ftp_client->list_files($remote_dir);
foreach($file_list as $file) {
	$ftp_url = "ftp://" . FTP_USER . ":" . FTP_PASS . "@" . FTP_SERVER . $remote_dir . $file;
	//printf("FTP URL: $ftp_url\n");
	if ($file == '.' || $file == '..' || $file == 'sofa-360' || $file == 'Rumor-Center-USA' || $file == 'Others' || $file == 'Furniture-Picture' || $file == 'ebay' || $file == 'ADJA' || $file == '20120617' || $file == '20120528') {
		//printf("Skipping directory - $file\n");
		continue;
	} else if (pathinfo($file, PATHINFO_EXTENSION)) {
		//printf("Skipping file - $file\n");
		continue;
	} else if (is_dir($ftp_url)) {
		$remote_subdir = $remote_dir . $file;
		$local_subdir = $local_dir . $file;
		if (is_dir($local_subdir)) {
			printf("Directory exists - $local_subdir\n");
			// Do Nothing
		} else {
			if (mkdir($local_subdir, 0755)) {
				printf("Directory created - $local_subdir\n");
			} else {
				printf("Failed to create - $local_subdir\n");
				continue;
			}
		}

		$ftp_client->change_dir($remote_subdir);
		$file_list2 = $ftp_client->list_files($remote_subdir);
		foreach($file_list2 as $file2) {
			if ($file2 == '.' || $file2 == '..') continue;
			$remote_path = $remote_subdir . "/" . $file2;
			$local_path = $local_subdir . "/" . $file2;
			if (file_exists($local_path)) {
				//printf("File exists - $file2\n");
			} else {
				printf("Downloading $local_path...");
				if ($ftp_client->get($local_path, $file2)) {
					printf("Downloaded\n");
				} else {
					printf("Failed to download\n");
				}
			}
		}
	}
}

// Disconnect from server
$ftp_client->disconnect();
?>
