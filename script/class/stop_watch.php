<?php
// Initialization
require_once("debugger.php");

class stop_watch {
	private $output;
	private $startTime;
	private $endTime;
	private $duration;

	public function __construct() {
		$this->output = new debugger;
	}

	public function stop_watch_start() {
		$this->startTime = microtime(true);
		$this->output->info("##### Start time: $startTime #####");
	}

	public function stop_watch_stop() {
		$this->endTime = microtime(true);
		$this->duration = $this->endTime - $this->startTime;
		$this->duration = round($this->duration, 2);
		$this->output->info("##### End time: $this->endTime #####");
		$this->output->info("##### Time executed: $this->duration seconds #####");
	}
}
