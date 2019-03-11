<?php

class App {
	
		private $input_parser;

		public $photos = [];
		public $photos_backup = [];
		public $photos_count = 0;

		public $slides = [];
		public $slides_backup = [];
		public $slides_count = 0;

		public $transitions = [];
		public $transitions_count = 0;

		public $slideshows = [];

		public $slideshow = [];
		public $slideshow_count = 0;

		public $slideshow_score = 0;

		public $photos_from_slideshow = [];

		private $timer;

		public function __construct($input_files, $active_index) {
			$this->timer = new Timer();

			$this->input_parser = new InputParser($input_files[$active_index]);
			$this->photos = $this->createPhotos();
			$this->photos_count = count($this->photos);
			dump('Photos: '. $this->photos_count);
			$this->sortPhotos();
			$this->photos_backup = $this->photos;
			dump('Photos sorted!');

			$this->createSlides();
			$this->slides_count = count($this->slides);
			dump('Slides: '. $this->slides_count);
			$this->slides_backup = $this->slides;
// dump(array_slice($this->slides, 0, 5));

			dump('Photos left: '. count($this->photos));

			$this->createTransitions();
			$this->transitions_count = count($this->transitions);
			dump('Transitions: '. $this->transitions_count);

			// dump('Slides left: '. count($this->slides));

			$this->sortTransitions();
			dump('Transitions sorted!');
			// dump($this->transitions);
// dump(array_slice($this->transitions, 0, 5));

			$this->createSlideshows();
			dump('Slideshows created: '. count($this->slideshows));
// dump(array_slice($this->slideshows, 0, 5));

			$this->sortSlideshows();
			dump('Slideshows sorted!');
// dump(array_slice($this->slideshows, 0, 5));
			$this->slideshow = $this->slideshows[0];
			dump('Slideshow score: '. $this->slideshow->score);
			$this->slideshow_count = count($this->slideshow->slides_ids);
			dump('Photos used in slideshow: '. $this->slideshow_count);

			// dump('Scorest slideshow is: ');
			// dump($this->slideshow);

			$this->convertSlideshowIntoPhotos();
			dump($this->photos_from_slideshow);
			
			$this->generateOutput($input_files[$active_index]);
		}


		private function createPhotos() {
			$photos = [];
			foreach ($this->input_parser->getFilesData() as $index => $set) {
				array_push($photos, new Photo($index, $set));
			}
			return $photos;
		}

		private function sortPhotos() {
			usort($this->photos, function($a, $b) {
				$a = $a->tags_count;
				$b = $b->tags_count;
				if ($a === $b) {
	        return 0;
		    }
		    return ($a > $b) ? -1 : 1;
			});			
		}

		private function createSlides() {
			$slide_id = 0;
			foreach ($this->photos as $photo_index => $photo) {
				if( is_null($this->photos[$photo_index]) ) continue;
				if ( $photo->type === 1 ) {
					array_push($this->slides, new Slide($slide_id, [
						'photos_ids' 	=> [$photo->id],
						'tags' 	=> $photo->tags
					]));
					unset($this->photos[$photo_index]);
					$slide_id++;
				} else if ( $photo->type === 2 && $photo->status ) {
					$relative_photo = $this->findRelativePhoto($photo, 2);
					if ( $relative_photo ) {
						array_push($this->slides, new Slide($slide_id, [
							'photos_ids' 	=> [$photo->id, $relative_photo->id],
							'tags' 	=> array_unique(array_merge($photo->tags, $relative_photo->tags))
						]));
						unset($this->photos[$photo_index]);
						$slide_id++;
					}
				}

				if( $this->timer->interval(10) ) {
					dump(count($this->slides) . ' slides have been created! '. count($this->photos) . ' photos left.');
				}

				if ( $this->timer->timeout(5600) ) {
					dump('CreateSlides() has been stopped after 5600 sec of execution! '. count($this->photos) . ' photos left.');
					dump(count($this->slides) . ' slides have been created!');
					return false;
				}
			}
		}

		private function findRelativePhoto($pivot_photo, $type = null) {
			foreach ($this->photos as $index => $photo) {
				if ( 	$pivot_photo->id !== $photo->id && 
							(is_null($type) || (!is_null($type) && $photo->type === $type)) &&
							empty($photo->commonTags($pivot_photo)) ) 
				{
					unset($this->photos[$index]);
					return $photo;
				}
			}

			return false;
		}

		private function createTransitions() {
			$transition_id = 0;

			foreach ( $this->slides as $slide_index => $slide ) {
			// for ($slide_index = 0; $slide_index < count($this->slides); $slide_index++) {
				// $slide = $this->slides[$slide_index];
				
				// if ( is_null($slide) ) continue;
				for( $i = 0; $i < $this->slides_count; $i++ ) {
					list($relative_slide, $commonTags) = $this->findRelativeSlide($slide, $i);

					if ( $relative_slide ) {
						$score = min(count($slide->tags), count($relative_slide->tags), count($commonTags));
						array_push($this->transitions, new Transition($transition_id, [
							'slides_ids' 	=> [$slide->id, $relative_slide->id],
							'score' 	=> $score
						]));
						// unset($this->slides[$slide_index]);
						$transition_id++;

						if( $this->timer->interval(10) ) {
							dump(count($this->transitions) . ' transitions have been created! '. (count($this->slides) - ($slide_index + 1)) . ' slides left. Timeout: '. (5600 - (time() - $this->timer->timeout_start)) .' seconds left.');
						}

						if ( $this->timer->timeout(5600) ) {
							dump('CreateTransitions() has been stopped after 5600 sec of execution! '. (count($this->slides) - ($slide_index + 1)) . ' slides left.');
							dump(count($this->transitions) . ' transitions have been created!');
							return false;
						}
					}
				}
				
			}
		}

		private function findRelativeSlide($pivot_slide, $start_index) {
			foreach (array_slice($this->slides, $start_index) as $index => $slide) {
				if ( $pivot_slide->id !== $slide->id ) {
					$commonTags = $slide->commonTags($pivot_slide);
					if ( !empty($commonTags) ) {					
						// unset($this->slides[$index]);
						return [$slide, $commonTags];
					}
				}
			}

			return [false, false];
		}

		private function sortTransitions() {
			usort($this->transitions, function($a, $b) {
				$a = $a->score;
				$b = $b->score;
				if ($a === $b) {
	        return 0;
		    }
		    return ($a > $b) ? -1 : 1;
			});
		}

		private function createSlideshows() {
			foreach ($this->transitions as $index => $transition) {
				$slideshow = new Slideshow($index);

				$slideshow->addSlide($transition->slides_ids[0]);
// dump('Transition '. $transition->slides_ids[0] . ' ' . $transition->slides_ids[1]);
				while( $transition ) {
					$slideshow->addSlide($transition->slides_ids[1], $transition->score);
					$transition = $this->searchNextTransition($slideshow->slides_ids, $transition->slides_ids[1], 5);
				}

				array_push($this->slideshows, $slideshow);

				if( $this->timer->interval(10) ) {
					dump(count($this->slideshows) . ' slideshows have been created! '. (count($this->transitions) - ($index + 1)) . ' transitions left.');
				}

				if ( $this->timer->timeout(7200) ) {
					dump('createSlideshows() has been stopped after 7200 sec of execution! '. (count($this->transitions) - ($index + 1)) . ' transitions left.');
					dump(count($this->slideshows) . ' slideshows have been created!');
					return false;
				}
			}
		}

		private function searchNextTransition($slide_used, $slide_id, $recursion_steps) {
			// dump($slide_id);
			foreach ($this->transitions as $index => $transition) {
				if ( $transition->slides_ids[0] === $slide_id && 
						 !in_array($transition->slides_ids[1], $slide_used)  ) 
				{
					// dump('$slide_id = '. $slide_id . ' & '. $transition->slides_ids[1] ." at step ". $recursion_steps);
					// dump($slide_used);
					if ( $recursion_steps ) {
						// array_push($slide_used, $transition->slides_ids[1]);
						// dump("Slides used ".count($slide_used));
						if ( $this->searchNextTransition($slide_used, $transition->slides_ids[1], --$recursion_steps) ) {
							return $transition;
						} else {
							// dump($transition->slides_ids[1] . ' is false!');
							// return $this->searchNextTransition($slide_used, $transition->slides_ids[1], --$recursion_steps);
							// dump("index ".$index." continue" );
							// dump($this->transitions[29]);
							continue;
						}
					} else {	
						return $transition;
					}
				}
			}
			return false;
		}

		private function sortSlideshows() {
			usort($this->slideshows, function($a, $b) {
				$a = $a->score;
				$b = $b->score;
				if ($a === $b) {
	        return 0;
		    }
		    return ($a > $b) ? -1 : 1;
			});
		}

		private function convertSlideshowIntoPhotos() {
			foreach ($this->slideshow->slides_ids as $slide_id) {
				array_push($this->photos_from_slideshow, $this->slides_backup[$slide_id]->photos_ids);
			}
		}

		private function generateOutput($input_file_name) {
			$output = new Output($this->photos_from_slideshow, $input_file_name);
			dump($output->result);
		}
}