<?
/* Initialization */
require_once('init.php');

if (isset($_POST['uid'])) {
	global $flatfair_file, $img_zip;
	$uid = $_POST['uid'];
	prepare($uid);
	echo "$flatfair_file,$img_zip";		
}

$flatfair_file = $img_zip = '';
function prepare($uid) {
	global $flatfair_file, $img_zip;
	$user_dir = "download/$uid/";
	$flatfair_file = $user_dir . 'flatfair.txt';
	$img_zip = $user_dir . 'images.zip';
}
?>
