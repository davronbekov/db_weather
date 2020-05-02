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
        require_once $_SERVER['DOCUMENT_ROOT'].'/db_weather/'.correcter($class).'.php';
    }

    spl_autoload_register('my_autoloader');