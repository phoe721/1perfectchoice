<?	
/* Initialization */
// test inventory
/*
require_once("inventory.php");
$inv = new inventory();
$inv->get_inventory("PDEX-101");
$inv->get_inventory("PDEX-F9231Q-F4735-36-37-38");
*/
// test shipping
/*
require_once("shipping.php");
$s = new shipping();
$cost = 10;
$length = 40;
$width = 23.25;
$height = 5.50;
$weight = 38;
$s->getUPSCost($cost, $length, $width, $height, $weight);
$s->getTruckingCost($weight);
$cuft = $s->getCuft($length, $width, $height);
$s->getPalletCount($cuft);
*/

// test keywords
/*
require_once("keywords.php");
$k = new keywords();
$str = "Anybody who programs in PHP can be a contributing member of the community that develops and deploys it; the task of deploying PHP, documentation and associated websites is a never ending one. With every release, or release candidate comes a wave of work, which takes a lot of organization and co-ordination.";
$k->get_keywords($str);
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
