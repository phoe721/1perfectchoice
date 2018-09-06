<?	
/* Initialization */
// costs test
require_once("costs.php");
$costs = new costs();
$file = fopen(UPLOAD . "input.txt", "r");
while(!feof($file)) {
	$line = trim(fgets($file));
	if (!empty($line)) {
		list($item, $cost) = explode("\t", $line);
		list($code, $item_no) = explode("-", $item);
		//echo "$code $item_no $cost" . PHP_EOL;
		$costs->update_cost($code, $item_no, $cost);
	}
}
fclose($file);

// discontinued test
/*
require_once("discontinued.php");
$dis = new discontinued();
$file = fopen(UPLOAD . "input.txt", "r");
while(!feof($file)) {
	$line = trim(fgets($file));
	if (!empty($line)) {
		list($code, $item_no) = explode("-", $line);
		$dis->check($code, $item_no);
	}
}
fclose($file);
*/

// ftp_client test 
/*
require_once("ftp_client.php");
$client = new ftp_client();
$client->connect("www.phoe721.com");
$client->login("aaron", "c7w2l181");
$client->list_files("/");
$client->set_passive();
$client->set_active();
$client->pwd();
$client->disconnect();
 */
?>
