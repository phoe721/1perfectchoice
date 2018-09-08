<?	
/* Initialization */
// test keywords
require_once("keywords.php");
$k = new keywords();
$str = "Anybody who programs in PHP can be a contributing member of the community that develops and deploys it; the task of deploying PHP, documentation and associated websites is a never ending one. With every release, or release candidate comes a wave of work, which takes a lot of organization and co-ordination.";
$k->get_keywords($str);

// test check_links
/*
require_once("init.php");
require_once("check_links.php");
$cl = new check_links();
$cl->check_link("http://php.net/manual/en/function.explode.php");
$cl->check_links_by_file(UPLOAD . "links.txt");
*/

// test get cost with set
/*
require_once("vendors.php");
require_once("set_list.php");
require_once("costs.php");
$vendors = new vendors();
$set_list = new set_list();
$costs = new costs();
$sku = "PDEX-F9153Q-F4569-70-71-72";
$code = $vendors->get_code($sku);
$set_list->check_by_sku($sku);
$set = $set_list->get_set_by_sku($sku);
var_dump($set);
for ($i = 0; $i < count($set); $i++) {
	echo $costs->get_cost($code, $set[$i]);
}
*/

// vendors test
/*
require_once("vendors.php");
$vendors = new vendors();
echo $vendors->get_record_count();
$vendors->check("AC");
$code = $vendors->get_code("AC-00156");
$name  = $vendors->get_name("AC");
echo $name; 
*/

// set_ist test
/*
require_once("set_list.php");
$set_list = new set_list();
echo $set_list->get_record_count();
$set_list->check("AC", "02018");
$set_list->get_set("AC", "02018");
*/

// costs test
/*
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
*/

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
