<?php
class Output {
	public $result = null;
	private $input_file_name = null;

	public function __construct($photos_groups, $input_file_name) {
		$this->input_file_name = $input_file_name;

		$content = count($photos_groups).PHP_EOL;
		foreach ($photos_groups as $group ) {
			$content .= implode(" ", $group) . PHP_EOL;
		}
		$content = trim($content);
		$write_result = file_put_contents('./output/'. $this->input_file_name, $content);

		if ( $write_result ) {
			$this->result = round($write_result/1024, 2) . 'KB have been written successfully into '. $this->input_file_name .'!';
		} else {
			$this->result = 'Error happens while write into '. $this->input_file_name .'!';
		}
	}
}