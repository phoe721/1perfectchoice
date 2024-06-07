<?
require_once("class/init.php");
require_once("class/debugger.php");
require_once("class/zipper.php");
require_once("class/helper_functions.php");
$year = "2019";
$inventory_path = SERVER2_INVENTORY_FOLDER . $year . "/";
$zipper = new zipper();
$output = new debugger();
$output->set_console(true);
$output->set_log_level(3);

if (is_dir($inventory_path)) {
	$output->info("Loop through each file in $inventory_path...");
	$dir = array_diff(scandir($inventory_path), array('..', '.'));
	foreach ($dir as $key => $file) {
		$file_path = $inventory_path . $file . "/";
		//$output->info($file_path);
		if(is_dir("$file_path")) {
			$subdir = array_diff(scandir($file_path), array('..', '.'));
			foreach($subdir as $key2 => $file2) {
				$file2_path = $file_path . $file2;
				//$output->info($file2_path);
				if (is_dir($file2_path)) {
					//$output->info("$file2_path is a directory, do nothing...");	
				} else if (preg_match("/.*\.zip/", $file2_path)) {
					$output->info("$file2_path is a zip file, unzip it...");
					$zipper->unzip_files($file2_path, $file_path);
					$output->info("Going to remove the zip file $file2_path...");
					unlink($file2_path);
				} else if (preg_match("/[0-9]{8}/", $file2_path)) {
					$file2_new_path = preg_replace("/ - /","-", $file2_path);
					rename($file2_path, $file2_new_path);
					$file2_path = $file2_new_path;
					$output->info("Found file $file2_path, going to move it to folder...");
					$file_name = basename($file2_path);
					$output->info("Found file $file_name, going to move it to folder...");
					list($platform, $datestr) = explode("-", pathinfo($file2_path, PATHINFO_FILENAME));
					$month_day_str = preg_replace("/" . $year . "/", "", $datestr);
					$month_day_folder = $file_path . $month_day_str . "/";
					if (!file_exists($month_day_folder)) mkdir($month_day_folder, 0777, true);
					$dest_path = $month_day_folder . $file_name;
					$output->info("Going to move $file2_path to $dest_path...");
					rename($file2_path, $dest_path);
				}
			}
		}
	}
} else {
	$output->error("$inventory_path is not a directory!");
}
?>
