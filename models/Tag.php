<?php
/**
 * 
 */
class Tag {
	public $name;
	public $photos = [];
	
	function __construct($name, $photos) {
		$this->name = $name;
		foreach ($photos as $photo) {
			if( $photo->hasTag($name) ) {
				array_push($this->photos, $photo->id);
			}
		}

		// usort($this->photos, function($a, $b) {
		// 	$a = count($a->getTags());
		// 	$b = count($b->getTags());
		// 	if ($a == $b) {
  //       return 0;
	 //    }
	 //    return ($a > $b) ? -1 : 1;
		// });
	}

	function getPhotos() {
		return $this->photos;
	}
}