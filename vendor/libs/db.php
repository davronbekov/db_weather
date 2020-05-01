<?php

namespace vendor\libs;

use PDO;

class DB{
	// Конфигурация базы данных
     const  HOSTNAME = "localhost:3306";
     const  DBNAME = "db_weather";
     const  USERNAME = "root";
     const  PASSWORD = "";

    public static $db;
    private static $j;

    public static function connectDB(){

        self::$db = new PDO("mysql:hostname=".self::HOSTNAME."; dbname=".self::DBNAME, self::USERNAME, self::PASSWORD);

        $codirovka = self::$db->prepare("SET NAMES `utf8`");
        $codirovka->execute();

    }
    public static function createTable($tn,$arr){

        $sql = "CREATE TABLE IF NOT EXISTS `".$tn."` (";
    foreach($arr as $k=>$v){

        if($v=="int_ai") $v = "INT NOT NULL AUTO_INCREMENT UNIQUE";

        $sql = $sql."`".$k."` ".$v.",";
    }
        $sql = $sql."PRIMARY KEY ( `id` ) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        $create_tables = self::$db->prepare($sql);
        $create_tables->execute();
    }
    public static function query($sql,$arr = null){
        if(empty(self::$db)){
            self::connectDB();
        }
        $dbres = self::$db->prepare($sql);
        for($i=0; $i<count($arr); $i++){
            $dbres->bindParam($i+1,self::$j[$i]);
        }
        for($i=0; $i<count($arr); $i++){
            self::$j[$i] = $arr[$i];
        }
        $dbres->execute();
        return  $dbres;
    }
}
