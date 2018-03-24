<?	
/* Initialization */
require_once("functions.php");

$server = "phoe721.com";
$user = "aaron";
$pass = "revive";
$files = array();
$conn = ftp_connect($server) or die("Couldn't connect to $server");
$input = UPLOAD . "input.txt";
$handle = fopen($input, "r");

// Login to FTP server
if (@ftp_login($conn, $user, $pass)) {
	echo "Successfully login to $server!" . PHP_EOL;
	ftp_pasv($conn, true); // Turn Passive Mode On
	if (ftp_chdir($conn, '/test')) {
		$files = ftp_nlist($conn, '.');
		if ($handle) {
			while (($line = fgets($handle)) != false) {
				$line = trim($line);
				foreach ($files as $file) {
					if (preg_match('/(\.)(gif|jpg|png|txt|swf|db|ico)$/i', $file)) {
						$sku = basename(str_replace(".jpg", "", $file));
						if (preg_match('/' . $sku . '/', $line)) {
							echo "Match: $line $sku" . PHP_EOL;
							echo "Going to copy $file to $line" . PHP_EOL;
							/*
							echo $path . PHP_EOL;
							if (ftp_get($conn, $file, $line, FTP_BINARY)) {
								echo "Copy file OK";
							} else {
								echo "Failed to copy file";
							}
							 */
						}
					}
				}
			}
		}
	}
} else {
	echo "Failed to login to $server!" . PHP_EOL;
}

fclose($handle);
ftp_close($conn);

?>
