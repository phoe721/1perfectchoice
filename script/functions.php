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

?>
