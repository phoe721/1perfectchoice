<?php
	/* Display Errors */
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	ini_set('error_log', '/var/log/php_error.log');
	ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36');
	error_reporting(E_ALL);

	/* Constants */
	define('ROOT', '/var/www/phoe721.com/project/1perfectchoice/');
	define('SCRIPT_ROOT', '/var/www/phoe721.com/project/1perfectchoice/script/');
	define('DOWNLOAD', ROOT . 'download/');
	define('UPLOAD', ROOT . 'upload/');
	define('INVENTORY', ROOT . 'inventory/');
	define('FILTER', ROOT . 'filter/');
	define('LOG_FILE', ROOT . 'log/1perfectchoice.log');
	define('MAX_UPLOAD_SIZE', 800000);
	define('TEST_NUMBER', 1000);
	define('BRAND', '1PerfectChoice');
	define('UPS_DIMENSION_WEIGHT_DIVIDER', 166);
	define('UPS_WEIGHT_LIMIT', 150);
	define('UPS_MEASUREMENT_LIMIT', 165);
	define('UPS_LENGTH_LIMIT', 108);
	define('UPS_FUEL_SURCHARGE', 6.75);
	define('UPS_LARGE_PACKAGE_LIMIT', 130);
	define('UPS_LARGE_PACKAGE_COST', 70);
	define('UPS_INSURANCE_RATE', 4.5);
	define('UPS_BASE_INSURANCE_COST', 9);
	define('UPS_BASE_INSURANCE_COVERAGE', 300);
	define('TRUCKING_BASE_COST', 375);
	define('TRUCKING_BASE_WEIGHT', 250);
	define('MAX_CUFT_ON_PALLET', 80);
	define('ONE_DAY_IN_SECONDS', 86400); // 24 * 60 * 60 = 86400 = 1 Day
	define('DB_SERVER', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', 'c7w2l181');
	define('DATABASE', '1perfectchoice');
?>
