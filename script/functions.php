<?
/* Initialization */
require_once("init.php");

function download($url, $path) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
	curl_setopt($ch, CURLOPT_NOPROGRESS, false); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$data = curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);

	$file = fopen($path, "w+");
	if ($file) {
		fputs($file, $data);
	} else {
		echo "[ERROR] Could not save $file to $path";
	}
	fclose($file);

	return file_exists($path);
}

function fetch_page($url, $timeout) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$data = curl_exec($ch);

	if (!curl_errno($ch)) {
		return $data;
	} else {
		echo 'Error: ' . curl_error($ch);
	}

	curl_close($ch);
}

function progress($download_size, $downloaded, $upload_size, $uploaded) {
	if ($download_size > 0) {
		$percentage = round(($downloaded / $download_size * 100), 0);
		//if (($percentage % 10) == 0) echo $percentage . ".";
	}
}

function zip_images() {
	global $img_dir, $img_zip, $result, $debug;
	$zip = new ZipArchive();

	if (file_exists($img_zip)) {
		unlink($img_zip);
	}

	if ($zip->open($img_zip, ZipArchive::CREATE)) {
		$files = scandir($img_dir);
		foreach($files as $file) {
			if (preg_match('/.*\.jpg/', $file)) {
				$path = $img_dir . $file;
				$zip->addFile($path, $file);
			}
		}
	} else {
		if ($debug) logger("[ERROR] Could not open archive");
	}


	$zip->close();
}

function grab_img($sku, $url) {
	global $img_dir;
	logger("Found link: " . $url);
	$img_path = $img_dir . $sku . ".jpg";
	if (file_exists($img_path)) $img_path = $img_dir . $sku . "-2.jpg";

	$result = download($url, $img_path);
	if ($result) {
		return $img_path;
	} else {
		return null;
	}
}

function upload_img_dir($server, $user, $pass, $directory, $img_dir) {
	$conn = ftp_connect($server) or die("Couldn't connect to $server");
	if (@ftp_login($conn, $user, $pass)) {
		ftp_pasv($conn, true); // Turn Passive Mode On
		$files = array_diff(scandir($img_dir), array('..', '.'));
		foreach ($files as $file) {
			$remote_file = "/images/" . $directory . "/" . $file;
			$img_file = $img_dir . $file;
			if (ftp_put($conn, $remote_file, $img_file, FTP_BINARY)) {
				log_result("Uploaded $file");
			} else {
				log_result("Failed to upload $file");
			}
		}
	} else {
		log_result("Failed to login FTP server!");
	}
	ftp_close($conn);
}

?>
