<?
require_once("class/check_links.php");
$cl = new check_links();
$cl->check_links_by_file(UPLOAD . "input.txt");

?>
