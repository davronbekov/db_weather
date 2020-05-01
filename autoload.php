<?php

	function correcter($str){
		$i = 0;
		$path = "";
		while ($i < strlen($str)) {
			if(substr($str, $i, 1) == '\\') $path .= '/';
			else $path .= substr($str, $i, 1);
			$i++;
		}
		return strtolower($path);
	}

	function my_autoloader($class) {
	    include './'.correcter($class).'.php';
	}

	spl_autoload_register('my_autoloader');
