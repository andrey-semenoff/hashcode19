<?php

class Photo {
	public $id;
	public $type;
	public $tags;
	public $tags_count;
	public $status;

	public function __construct($id, $settings) {
		$this->id = $id;
		$this->type = $settings[0] === 'H' ? 1 : 2;
		$this->tags_count = +$settings[1];
		$this->tags = array_slice($settings, 2);
		$this->status = true;
	}

	public function hasTag($name) {
		return in_array($name, $this->tags);
	}

	public function commonTags($photo) {
		return array_intersect($this->tags, $photo->tags);
	}

	public function getTags()
	{
		return $this->tags;
	}
}