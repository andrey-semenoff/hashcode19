<?php

class Slide {
	public $id;							// Integer: 	ID of Slide
	public $photos_ids;			// Integer[]:	IDs of photos in slide
	public $tags;						// String[]:	Amount of photo tags

	public function __construct($id, $settings) {
		$this->id = $id;
		$this->photos_ids = $settings['photos_ids'];
		$this->tags = $settings['tags'];
	}

	public function commonTags($slide) {
		return array_intersect($this->tags, $slide->tags);
	}

}