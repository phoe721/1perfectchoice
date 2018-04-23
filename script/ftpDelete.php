<?
/* Initialization */
require_once('functions.php');

$server = "www.3pxusa.com";
$user = "aaron@3pxusa.com";
$pass = "c7w2l181";
$folder = "PDEX_WB";
$files = array();
$conn = ftp_connect($server) or die("Couldn't connect to $server");
$input = UPLOAD . "input.txt";

// Login to FTP server
if (@ftp_login($conn, $user, $pass)) {
	echo "Successfully login to $server!" . PHP_EOL;
	ftp_pasv($conn, true); // Turn Passive Mode On
	$path = "/images/" . $folder;
	$files = ftp_nlist($conn, $path);
	if (ftp_chdir($conn, $path)) {
		echo "Changing directory to: " . ftp_pwd($conn) . PHP_EOL;
		$handle = fopen($input, "r");
		if ($handle) {
			while (!feof($handle)) {
				$sku = trim(fgets($handle));
				$file = $sku . ".jpg";
				if (in_array($file, $files)) {
					if (ftp_delete($conn, $file)) {
						echo "Success to remove $file!" . PHP_EOL;
					} else {
						echo "Failed to remove $file!" . PHP_EOL;
					}
				}
			}
		}
		fclose($handle);
	} else {
		echo "Failed to change directory to $path!" . PHP_EOL;
	}
} else {
	echo "Failed to login to $server!" . PHP_EOL;
}

ftp_close($conn);

?>
