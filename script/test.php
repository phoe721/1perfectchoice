<?
require_once("class/ftp_client.php");
$ftp_client = new ftp_client();
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();

$inputFile = UPLOAD . "input.txt";
$outputFile = DOWNLOAD . "output.txt";
$input = fopen($inputFile, "r");
$output = fopen($outputFile, "a+");
if ($input && $output) {
	while(!feof($input)) {
		$line = trim(fgets($input));
		if (!empty($line)) {
			list($sku, $url) = explode("\t", $line, 2);
			$path = DOWNLOAD . $sku . ".jpg";
			$ftp_client->get($path, $url);
			echo "$url Downloaded" . PHP_EOL;
			$result = "$url\tDownloaded" . PHP_EOL;
			fwrite($output, $result);
		}
	}
}
fclose($input);
fclose($output);
$ftp_client->disconnect();
?>
