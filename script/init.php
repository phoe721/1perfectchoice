<?php
	/* Display Errors */
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36');
	error_reporting(E_ALL);

	/* Constants */
	define('ROOT', '/var/www/html/phoe721.com/project/1perfectchoice/');
	define('SCRIPT_ROOT', '/var/www/html/phoe721.com/project/1perfectchoice/script/');
	define('DOWNLOAD', ROOT . 'download/');
	define('UPLOAD', ROOT . 'upload/');
	define('LOG_FILE', ROOT . 'log/1perfectchoice.log');
	define('QUEUE_LOG', ROOT . 'log/queue.log');
	define('HOUSEKEEPING_LOG', ROOT . 'log/housekeeping.log');
	define('TEST_NUMBER', 1000);
	define('BRAND', '1PerfectChoice');

	/* Set user agent */
	$_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36";
?>
