<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 3:13 PM
 */

namespace vendor\models;

use vendor\libs\DB;

/**
 * Class Service
 * @package vendor\models
 * @property String $name
 * @property String $params_url
 * @property String $license
 */
class Service
{
    public static $table = 'service';

    public static $fillable = [
        'name', 'params_url', 'license'
    ];

    public static function getItem($data = []){
        $query = 'SELECT * FROM `'.self::$table.'` WHERE 1=1';

        $queryValues = [];

        foreach (self::$fillable as $attr){
            if(isset($data[$attr])){
                $query .= ' and `'.$attr.'` like ?';
                $queryValues[] = $data[$attr];
            }
        }

        $item = DB::query($query, $queryValues);

        return $item->fetch();
    }

    public static function updateItem($data = [], $filter = []){
        $query = 'UPDATE `'.self::$table.'` SET ';

        $queryValues = [];

        $counter = 0;
        foreach (self::$fillable as $attr){
            if(isset($data[$attr])){
                $query .= '`'.$attr.'` = ?';
                $queryValues[] = $data[$attr];

                if(count($data) > ++$counter){
                    $query .= ',';
                }
            }
        }

        $query .= ' WHERE 1=1';

        foreach (self::$fillable as $attr){
            if(isset($filter[$attr])){
                $query .= ' and `'.$attr.'` like ?';
                $queryValues[] = $filter[$attr];
            }
        }

        $item = DB::query($query, $queryValues);

        return $item->fetch();
    }

    public static function insertItem($data = []){
        $query = 'INSERT INTO `'.self::$table.'`(';

        $queryValues = [];

        $counter = 0;
        foreach (self::$fillable as $attr){
            if(isset($data[$attr])){
                $query .= '`'.$attr.'`';
                $queryValues[] = $data[$attr];

                if(count($data) > ++$counter){
                    $query .= ',';
                }
            }
        }

        $query .= ') VALUES (';

        $counter = 0;
        foreach (self::$fillable as $attr){
            if(isset($data[$attr])){
                $query .= '?';

                if(count($data) > ++$counter){
                    $query .= ',';
                }
            }
        }

        $query .= ')';

        $item = DB::query($query, $queryValues);

        return $item->fetch();
    }

    public static function deleteItem($data = []){
        $query = 'DELETE FROM `'.self::$table.'` WHERE 1=1';

        $queryValues = [];

        foreach (self::$fillable as $attr){
            if(isset($data[$attr])){
                $query .= ' and `'.$attr.'` like ?';
                $queryValues[] = $data[$attr];
            }
        }

        $item = DB::query($query, $queryValues);

        return $item->fetch();
    }

    public static function getItems($filter = []){
        $query = 'SELECT * FROM `'.self::$table.'` WHERE 1=1';

        $queryValues = [];

        foreach (self::$fillable as $attr){
            if(isset($filter[$attr])){
                $query .= ' and `'.$attr.'` like ?';
                $queryValues[] = $filter[$attr];
            }
        }

        $item = DB::query($query, $queryValues);

        return $item->fetchAll();
    }
}