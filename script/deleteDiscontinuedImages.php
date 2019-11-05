<?
require_once("class/ftp_client.php");
require_once("class/discontinued.php");
require_once("class/validator.php");
$ftp_client = new ftp_client();
$discontinued = new discontinued();
$validator = new validator();

// Connect to server
$ftp_client->connect(FTP_SERVER);
$ftp_client->login(FTP_USER, FTP_PASS);
$ftp_client->set_passive();

$list = $discontinued->get_list();
$total = count($list);
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	$file = "$item_no.jpg";
	$ftp_client->change_dir("/images/$code");

	if($ftp_client->size($file) > 0 && $ftp_client->delete($file)) {
		printf("Deleting image for $sku - It's discontinued!\n");
		$removeCount++;
	} else {
		//printf("$file not found on web server!\n");
	}
	//$url = IMAGE_SERVER . "$code/$item_no.jpg";
	//if ($validator->check_url($url)) {
	//} else {
		//printf("$file not found on web server!\n");
	//}
}
printf("Total: %d Removed: %d\n", $total, $removeCount);

// Disconnect from server
$ftp_client->disconnect();
?>
