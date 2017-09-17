<?php
if (isset($_POST['getUID'])) {
	$uid = uniqid();
	echo $uid;
}
?>
