<?
require_once("class/init.php");
require_once("class/status.php");
$status = new status();

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
				list($sku, $uri) = explode("\t", $line);
				$status->log_status("Converting $sku to jpg...");
				$uri = str_replace('data:image/webp;base64,', '', $uri);
				$uri = str_replace(' ', '+', $uri);
				$data = base64_decode($uri);
				$file = DOWNLOAD . $sku . '.webp';
				if (file_put_contents($file, $data)) {
					$result = "Convert OK: $sku\t$file" . PHP_EOL;
				} else {
					$result = "Failed to convert: $sku\t$file" . PHP_EOL;
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
