<?php

// include composer autoload
require 'vendor/autoload.php';
require './db.php';

header("Location: /db_weather");
setcookie('session_id', null, -1, '/');
?>
