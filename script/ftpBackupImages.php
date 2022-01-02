<?
require_once("class/ftp_client.php");
$ftp_client = new ftp_client();

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();
//$ftp_client->set_active();

$remote_dir = "/public_html/images/";
$local_dir = IMG;
$ftp_client->change_dir($remote_dir);
$file_list = $ftp_client->get_list_files($remote_dir);
foreach($file_list as $file) {
	$ftp_url = "ftp://" . FTP_USER . ":" . FTP_PASS . "@" . FTP_SERVER . $file;
	//printf("FTP URL: $ftp_url\n");
	if (pathinfo($file, PATHINFO_EXTENSION)) {
		//printf("Skipping file - $file\n"); // Skipping files in image first directory
		continue;
	} else if (is_dir($ftp_url)) {
		$filename = basename($file);
		if ($filename == '.' || $filename == '..' || $filename == 'sofa-360' || $filename == 'Rumor-Center-USA' || $filename == 'Others' || $filename == 'Furniture-Picture' || $filename == 'ebay' || $filename == 'ADJA' || $filename == '20120617' || $filename == '20120528') {
			//printf("Skipping this directory - $filename\n"); // Skipping irrelevant directories
			continue;
		} else {
			$local_subdir = $local_dir . $filename;
			if (is_dir($local_subdir)) {
				// Do Nothing
				printf("Directory exists - $local_subdir\n");
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
			$fi = new FilesystemIterator($local_subdir, FilesystemIterator::SKIP_DOTS);
			$local_file_count = iterator_count($fi); // Get local files count
			$remote_file_count = count($file_list2) - 2; //Get remote files count. Don't count dots directories
			printf("There are %d local files and %d remote files.\n", $local_file_count, $remote_file_count);
			if ($local_file_count != $remote_file_count) {
				foreach($file_list2 as $file2) {
					if (pathinfo($file2, PATHINFO_EXTENSION)) {
						$filename2 = basename($file2);
						if ($filename2 == '.' || $filename2 == '..') continue; // Skip dots directories
						$local_path = $local_subdir . "/" . $filename2;
						if (!file_exists($local_path)) {
							printf("Downloading $local_path...");
							if ($ftp_client->get($local_path, $file2)) {
								printf("Downloaded\n");
							} else {
								printf("Failed to download\n");
							}
						} else {
							$remote_mtime = $ftp_client->mdtm($file2);
							$local_mtime = filemtime($local_path);
							if ($local_mtime > $remote_mtime) {
								printf("File exists - $file2\n");
							} else {
								printf("Downloading $local_path...");
								printf("From $file2...");
								if ($ftp_client->get($local_path, $file2)) {
									printf("Downloaded\n");
								} else {
									printf("Failed to download\n");
								}
							}
						}
					}
				}
			} else {
				// Files count are the same.
				printf("Files count are the same. Nothing to update!\n");
			}
		}

	}
}

// Disconnect from server
$ftp_client->disconnect();
?>
