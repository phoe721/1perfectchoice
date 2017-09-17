<?
/* Initialization */
require_once('init.php');
require_once('functions.php');
require_once('simple_html_dom.php');

define("VENDOR_CODE", "AC-");
$query = "http://ww2.acmecorp.com/catalogsearch/result/?q=";
$list_file = $sku_list = $status_file = "";
if (isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);

	$file = fopen($sku_list, "r") or die("Unable to open file!");
	$file2 = fopen($output_list, "a+") or die("Unable to open file!");
	if ($file) {
		while (($sku = fgets($file)) != false) {
			$sku = trim($sku);
			$queryURL = $query . $sku; 
			$page = file_get_html($queryURL);
			$check = $page->find('h1 a', 0);
	
			$link = $dimension = $weight = "";
			if (isset($check)) {
				$link = $page->find('h1 a', 0)->href;
	
				$dimension = $page->find('div.pull-left p', 0)->plaintext;
				if (isset($dimension)) {
					$dimension = trim(preg_replace('/Dimensions \:/', '', $dimension));
					$dimension = trim(preg_replace('/N\/A/', '', $dimension));
				}
	
				$package = $page->find('div.pull-left p', 1)->plaintext;
				if (isset($package)) {
					$package = trim(strip_tags(preg_replace('/Package \:/', '', $package)));
					$pieces = explode('/', $package);
					if (isset($pieces[3])) {
						$weight = preg_replace('/LBS/i', '', $pieces[3]);
					}
				}
			}
	
			$output = "$sku\t$link\t$dimension\t$weight\n";
			log_status('Finishing ' . $sku . ' from ACME');
			fwrite($file2, $output);
		}
	}
	fclose($file);
	fclose($file2);
	
	$file2 = fopen($output_list, "r") or die("Unable to open file!");
	if ($file2) {
		while (($line = fgets($file2)) != false) {
			$line = trim($line);
			list($sku, $link, $blank) = explode("\t", $line);
	
			if (isset($link) && !empty($link)) {
				$page = file_get_html($link);
				if (isset($page)) {
					$imgURL = $page->find('#zoom1', 0)->href;
					$filename = IMG_FOLDER . VENDOR_CODE . $sku . "-2.jpg";
					echo $imgURL . " " . $filename . "\n";
					file_put_contents($filename, file_get_contents($imgURL));
					log_status('Downloaded ' . $sku . ' image from ACME');
				}
			}
		}
	}
	fclose($file2);
	log_status('ACME Done!');
}

function prepare($uid) {
	global $sku_list, $output_list, $status_file;
	$user_dir = AMAZON_DOWNLOAD . $uid . '/';
	$img_dir = $user_dir . '/img/';
	$sku_list = $user_dir . 'sku_list';
	$output_list = $user_dir . 'output_list';
	$status_file = $user_dir . 'status';
}

function log_status($status) {
	global $status_file;
	$file = fopen($status_file, 'w') or die('Unable to open file!');
	fwrite($file, $status);
	fclose($file);
}
?>
