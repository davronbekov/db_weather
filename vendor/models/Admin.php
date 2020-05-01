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
}