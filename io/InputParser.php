<?php

class InputParser {
	private $file;
	private $photos_data = [];

	public function __construct($filename) {
		$this->file = file('./input_data/' . $filename);
		array_shift($this->file);

		foreach ($this->file as $line) {
			array_push($this->photos_data, explode(' ', trim($line)));
		}
	}

	public function getFilesData() {
		return $this->photos_data;
	}



}