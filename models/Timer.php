<?php

class Timer {
	public $timeout_start = 0;
	public $interval_start = 0;

	public function __construct($settings = null) {
		$this->timeout_start = time();
	}

	public function timeout($seconds) {
		if( (time() - $this->timeout_start) >= $seconds ) {
			return true;
		} else {
			return false;
		}
	}

	public function interval($seconds) {
		if( (time() - $this->interval_start) >= $seconds ) {
			$this->interval_start = time();
			return true;
		} else {
			return false;
		}
	}
}