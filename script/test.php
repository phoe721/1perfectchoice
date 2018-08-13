<?	
/* Initialization */
require_once("discontinued.php");
$dis = new discontinued();
echo $dis->get_record_count();

?>
