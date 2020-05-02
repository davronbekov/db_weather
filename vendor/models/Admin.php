<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 10:54 PM
 */

namespace vendor\models;

use vendor\libs\DB;

/**
 * Class Admin
 * @package vendor\models
 * @property String $aname_first
 * @property String $aname_last
 * @property String $rights
 * @property String $login
 */
class Admin
{
    public static $table = 'admin';

    public static $fillable = [
        'aname_first', 'aname_last', 'rights', 'login'
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
}