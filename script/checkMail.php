<?
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

/*
$hostname = '{localhost:143}INBOX';
$username = 'www-data@phoe721.com';
$password = 'c7w2l181';

$mbox = imap_open("$hostname", "$username", "$password");

echo "<h1>Mailboxes</h1>\n";
$folders = imap_listmailbox($mbox, "$hostname", "*");

if ($folders == false) {
	echo "Call failed<br />\n";
} else {
	foreach ($folders as $val) {
		echo $val . "<br />\n";
	}
}

echo "<h1>Headers in INBOX</h1>\n";
$headers = imap_headers($mbox);

if ($headers == false) {
	echo "Call failed<br />\n";
} else {
	foreach ($headers as $val) {
		echo $val . "<br />\n";
	}
}

imap_close($mbox);
 */
?>
