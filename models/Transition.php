<?php

class Transition {
	public $id;							// Integer: 	ID of Transition
	public $slides_ids;			// Integer[]: IDs of Slides
	public $score;					// Integer: 	Score for Transition

	public function __construct($id, $settings) {
		$this->id = $id;
		$this->slides_ids = $settings['slides_ids'];
		$this->score = $settings['score'];
	}
}