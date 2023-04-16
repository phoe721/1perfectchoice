<?
require_once('class/init.php');
require_once('class/cleaner.php');
$cl = new cleaner;

// Check download directory
$cl->remove_outdated_files(DOWNLOAD);
$cl->remove_empty_dir(DOWNLOAD);

// Check upload directory
$cl->remove_outdated_files(UPLOAD);
$cl->remove_empty_dir(UPLOAD);
?>
