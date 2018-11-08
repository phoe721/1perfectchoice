<?
require_once("class/validator.php");
require_once("class/status.php");
$validator = new validator();
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
			$url = trim(fgets($input));
			if (!empty($url)) {
				$status->log_status("Checking $url...");
				if ($validator->check_url($url)) {
					$result = "$url\tOK" . PHP_EOL;
				} else {
					$result = "$url\tFail" . PHP_EOL;
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
