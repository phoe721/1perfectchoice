<?
require_once("class/debugger.php");
$output = new debugger();
$output->set_console(true);

/* Connect to Local Mail*/
$output = mailparse_msg_parse_file("/var/mail/www-data");
var_dump($output);

if (mailparse_msg_free($output)) {
	$output->info("MIME resource freed!");
} else {
	$output->info("Failed to free MIME resource!");
}
?>
