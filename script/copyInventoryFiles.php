<?
require_once("class/init.php");
require_once("class/debugger.php");
require_once("class/zipper.php");
require_once("class/helper_functions.php");
$remote_dir = SERVER2_INVENTORY_FOLDER . date("Y") . "/" . date("n") . "/";
$zipper = new zipper();
$output = new debugger();
$output->set_console(true);
$output->set_log_level(2);
$date_str = date("Ymd");
$today_str = date("md");

if (is_dir($remote_dir)) {
	$output->info("Loop through each file in $remote_dir...");
	$dir = array_diff(scandir($remote_dir), array('..', '.'));
	foreach ($dir as $key => $file) {
		if(is_dir("$file")) {
			$output->info("$file is a drectory, do nothing...");
		} else {
			if (preg_match("/.*-" . $date_str . "/", $file)) {
				$output->info("$file is today's inventory file, rename it first...");
				if (preg_match("/DSC/", $file)) {
					$new_name = preg_replace("/" . $date_str . "/", "", $file);
				} else {
					$new_name  = preg_replace("/-" . $date_str . "/", "", $file);
				}
				$source_file = $remote_dir . $file;
				$target_file = INVENTORY . $new_name;
				$output->info("Going to copy $soruce_file to $target_file");
				if (!copy($source_file, $target_file)) {
					$output->notice("Failed to copy $source_file");
				} else {
					$output->info("$source_file copied to $target_file successfully");;
				}
			} else if (preg_match("/" . $today_str . "\.zip/", $file)) {
				$output->info("$file is today's inventory file, unzip it first...");
				$zip_file = $remote_dir . $file;
				$extract_dir = INVENTORY . $today_str . "/";
				$zipper->unzip_files($zip_file, INVENTORY);
				$output->info("Loop through each file in $extract_dir...");
				$dir2 = array_diff(scandir($extract_dir), array('..', '.'));
				foreach ($dir2 as $key => $file2) {
					$output->info("Copy only Amazon, Houzz, Overstock, and Wayfair inventory files...");
					if (preg_match("/Amazon*|Houzz*|Overstock*|Wayfair*/", $file2)) {
						$source_path = $extract_dir . $file2;
						$target_path = INVENTORY . $file2;
						if (!copy($source_path, $target_path)) {
							$output->notice("Failed to copy $source_path");
						} else {
							$output->notice("$source_path copied to $target_path successfully");;
						}
					}
				}
				$output->info("Going to remove $extract_dir...");;
				rrmdir($extract_dir);
			} else {
				$output->info("$file is not today's inventory file");
			}
		}
	}
} else {
	$output->error("$remote_dir is not a directory!");
}
?>
