<?php
// Initialization
require_once("debugger.php");
require_once("init.php");

class zipper {
	private $output;

	public function __construct() {
		$this->output = new debugger;
	}

	public function zip_files($sourceDir, $targetFile) {
		$zip = new ZipArchive();
		if ($zip->open($targetFile, ZipArchive::CREATE)) {
			$files = scandir($sourceDir);
			foreach($files as $file) {
				$this->output->info("Adding $file to $targetFile...");
				$path = $sourceDir . $file;
				$zip->addFile($path, $file);
			}
		} else {
			$this->output->error("Could not open archive!");
		}

		$zip->close();
	}

	public function unzip_files($sourceFile, $targetDir) {
		$zip = new ZipArchive();
		if ($zip->open($sourceFile)) {
			$zip->extractTo($targetDir);
			$this->output->info("Extracting $sourceFile to $targetDir...");
		} else {
			$this->output->error("Could not open archive!");
		}

		$zip->close();
	}
}
