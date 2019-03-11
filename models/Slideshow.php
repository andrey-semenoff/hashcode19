<?php

class Slideshow {
	public $id;							// Integer: 	ID of Slideshow
	public $slides_ids;			// Integer[]:	IDs of slides in slideshow
	public $score;					// Integer:		Score of slideshow

	public function __construct($id) {
		$this->id = $id;
		$this->slides_ids = [];
		$this->score = 0;
	}

	public function addSlide($slide_id, $score = 0) {
		array_push($this->slides_ids, $slide_id);
		$this->score += $score;
	}
}