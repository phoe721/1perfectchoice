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

	public function start() {
		$this->startTime = microtime(true);
		$this->output->notice("##### Start time: $startTime #####");
	}

	public function stop() {
		$this->endTime = microtime(true);
		$this->duration = $this->endTime - $this->startTime;
		$this->duration = round($this->duration, 2);
		$this->output->notice("##### End time: $this->endTime #####");
		$this->output->notice("##### Time executed: $this->duration seconds #####");
	}
}
