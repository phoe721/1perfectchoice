<?
require_once("class/ftp_client.php");
$ftp_client = new ftp_client();
$remote_dir = "/public_html.1611423940.bak/images/";
$local_dir = "/home/aaron/images/";

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();
//$ftp_client->set_active();
$ftp_client->change_dir($remote_dir);
$file_list = $ftp_client->get_list_files($remote_dir);
foreach($file_list as $file) {
	$ftp_url = "ftp://" . FTP_USER . ":" . FTP_PASS . "@" . FTP_SERVER . $file;
	//printf("FTP URL: $ftp_url\n");
	if (pathinfo($file, PATHINFO_EXTENSION)) {
		//printf("Skipping file - $file\n");
		continue;
	} else if (is_dir($ftp_url)) {
		$filename = basename($file);
		if ($filename == '.' || $filename == '..' || $filename == 'sofa-360' || $filename == 'Rumor-Center-USA' || $filename == 'Others' || $filename == 'Furniture-Picture' || $filename == 'ebay' || $filename == 'ADJA' || $filename == '20120617' || $filename == '20120528') {
			printf("Skipping this directory - $filename\n");
			continue;
		} else {
			$local_subdir = $local_dir . $filename;
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

			$ftp_client->change_dir($file);
			$file_list2 = $ftp_client->get_list_files($file);
			foreach($file_list2 as $file2) {
				$filename2 = basename($file2);
				if ($filename2 == '.' || $filename2 == '..') continue;
				$local_path = $local_subdir . "/" . $filename2;
				if (file_exists($local_path)) {
					$remote_mtime = $ftp_client->mdtm($file2);
					$local_mtime = filemtime($local_path);
					if ($local_mtime > $remote_mtime) {
						//printf("File exists - $file2\n");
					} else {
						printf("Downloading $local_path...");
						printf("From $file2...");
						if ($ftp_client->get($local_path, $file2)) {
							printf("Downloaded\n");
						} else {
							printf("Failed to download\n");
						}
					}
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
}

// Disconnect from server
$ftp_client->disconnect();
?>
