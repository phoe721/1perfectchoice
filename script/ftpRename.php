<?
/* Initialization */
require_once('functions.php');

$server = "www.3pxusa.com";
$user = "aaron@3pxusa.com";
$pass = "c7w2l181";
$folder = "ZM";
$replace = "ZM-";
$files = array();
$conn = ftp_connect($server) or die("Couldn't connect to $server");

// Login to FTP server
if (@ftp_login($conn, $user, $pass)) {
	echo "Successfully login to $server!" . PHP_EOL;
	ftp_pasv($conn, true); // Turn Passive Mode On
	$path = "/images/" . $folder;
	$files = ftp_nlist($conn, $path);
	foreach ($files as $file) {
		if (preg_match('/(\.)(gif|jpg|png|txt|swf|db|ico)$/i', $file)) {
			//echo "Before renaming: " . $file . PHP_EOL;
			$new_file = preg_replace('/' . $replace . '/', '', $file);
			//echo "Going to rename to: " . $new_file . PHP_EOL;
			if (ftp_chdir($conn, $path)) {
				//echo "Changing directory to: " . ftp_pwd($conn) . PHP_EOL;
				if (ftp_rename($conn, $file, $new_file)) {
					echo "Success to change $file to $new_file" . PHP_EOL;
				} else {
					echo "Failed to change $file name!" . PHP_EOL;
				}
			} else {
				echo "Failed to change directory to $path!" . PHP_EOL;
			}
		}
	}
} else {
	echo "Failed to login to $server!" . PHP_EOL;
}

ftp_close($conn);

?>
