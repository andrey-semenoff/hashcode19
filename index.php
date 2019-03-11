<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Hashcode19</title>
</head>
<body>
	<?php
		require_once './utils/help_functions.php';
		require_once './io/InputParser.php';
		require_once './io/Output.php';
		require_once './models/Timer.php';
		require_once './models/Photo.php';
		require_once './models/Tag.php';
		require_once './models/Slide.php';
		require_once './models/Transition.php';
		require_once './models/Slideshow.php';
		require_once './models/App.php';

		$input_files = [
			'a_example.txt',
			'b_lovely_landscapes.txt',
			'c_memorable_moments.txt',
			'd_pet_pictures.txt',
			'e_shiny_selfies.txt'
		];

		$active_index = 2; // Index of active Input_file

		$app = new App($input_files, $active_index);
		
	?>
</body>
</html>