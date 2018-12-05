<?
require_once("class/downloader.php");
require_once("class/status.php");
require_once("class/validator.php");
$downloader = new downloader();
$status = new status();
$validator = new validator();

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
			$status->log_status("Processing $line...");
			if (!empty($line)) {
				list($sku, $url) = explode("\t", $line, 2);
				if ($validator->check_sku($sku) && $validator->check_url($url)) {
					$path = DOWNLOAD . $sku . ".jpg";
					$downloader->download($url, $path);
					$result = "$line\tDownloaded" . PHP_EOL;
				} else {
					$result = "$line\tInvalid" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}
	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}
?>
