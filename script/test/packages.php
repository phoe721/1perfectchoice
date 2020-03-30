<?
require_once("../class/database.php");
$db = database::getInstance();

for ($i = 1; $i <=10; $i++) {
	$cmd1 = "UPDATE packages SET box" . $i . "_length = 0 WHERE box" . $i . "_length IS NULL";
	$cmd2 = "UPDATE packages SET box" . $i . "_width = 0 WHERE box" . $i . "_width IS NULL";
	$cmd3 = "UPDATE packages SET box" . $i . "_height = 0 WHERE box" . $i . "_height IS NULL";
	$cmd4 = "UPDATE packages SET box" . $i . "_weight = 0 WHERE box" . $i . "_weight IS NULL";

	$result = $db->query($cmd1);
	if ($result) {
		echo "$cmd1 - OK" . PHP_EOL;
	} else {
		echo $db->error();
	}

	$result = $db->query($cmd2);
	if ($result) {
		echo "$cmd2 - OK" . PHP_EOL;
	} else {
		echo $db->error();
	}

	$result = $db->query($cmd3);
	if ($result) {
		echo "$cmd3 - OK" . PHP_EOL;
	} else {
		echo $db->error();
	}

	$result = $db->query($cmd4);
	if ($result) {
		echo "$cmd4 - OK" . PHP_EOL;
	} else {
		echo $db->error();
	}
}
?>
