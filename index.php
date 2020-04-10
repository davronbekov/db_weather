<?php

// include composer autoload
require 'vendor/autoload.php';
require './db.php';
//
//// import the Intervention Image Manager Class
//use Intervention\Image\ImageManager;
//
//// create an image manager instance with favored driver
//$manager = new ImageManager();
//
//// to finally create image instances
//$image = $manager->make('public/minion.jpg')->resize(300, 200);
//
//$image->save('public/minion.2.jpg');

DB::query("SELECT * FROM `User`");
?>

<img src="public/minion.2.jpg">
