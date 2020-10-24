<?
require_once("class/orders.php");
require_once("class/status.php");
require_once("class/validator.php");
$orders = new orders();
$status = new status();
$validator = new validator();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$line = trim(fgets($input));
			if (!empty($line)) {
				$status->log_status("Inserting $line...");
				list($id, $sku, $city, $state, $country, $price, $qty, $date, $platform) = explode("\t", $line);
				$pd = date_parse($date);
				$date_str = $pd['year'] . "-" . $pd['month'] . "-" . $pd['day'] . " " . $pd['hour'] . ":" . $pd['minute'] . ":" . $pd['second'];
				if ($validator->check_sku($sku)) {
					list($code, $item_no) = explode("-", $sku, 2);
					if ($orders->check_exist($id)) {
						$result = "$id\tExists". PHP_EOL;
					} else {
						$result = $orders->insert($id, $code, $item_no, $city, $state, $country, $price, $qty, $date_str, $platform);
						$result = $result ? "$id\tOK" . PHP_EOL : "$id\tFail" . PHP_EOL;
					}
				} else {
					$result = "$sku\tInvalid" . PHP_EOL;
				}
				fwrite($output, $result);
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
} else if (isset($argv[1])) {
	$line = $argv[1];
	if (!empty($line)) {
		printf("Inserting $line...\n");
		list($id, $sku, $city, $state, $country, $price, $qty, $date, $platform) = explode("|", $line);
		$pd = date_parse($date);
		$date_str = $pd['year'] . "-" . $pd['month'] . "-" . $pd['day'] . " " . $pd['hour'] . ":" . $pd['minute'] . ":" . $pd['second'];
		printf("$date_str\n");
		if ($validator->check_sku($sku)) {
			list($code, $item_no) = explode("-", $sku, 2);
			if ($orders->check_exist($id)) {
				printf("$id\tExists\n");
			} else {
				$result = $orders->insert($id, $code, $item_no, $city, $state, $country, $price, $qty, $date_str, $platform);
				printf("$id\tInserted\n");
			}
		} else {
			printf("$id\tInvalid\n");
		}
	}
}
?>
