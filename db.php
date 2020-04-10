<?php

//namespace alphazet\main;
use Noodlehaus\Config;
require 'vendor/autoload.php';

class DB{
    // Конфигурация базы данных
    const  HOSTNAME = "localhost:3306";
    const  DBNAME = "db_weather";
    const  USERNAME = "root";
    const  PASSWORD = "tiger";

    public static $db;
    private static $j;

    public static function connectDB(){
        $CONFIG = new Config('config/db.json');


        self::$db = new PDO("mysql:hostname=".$CONFIG->get('db.host')."; dbname=".$CONFIG->get('db.name'), $CONFIG->get('db.user'), $CONFIG->get('db.password'));

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
//    session_start();
?>
