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
 * Class Person
 * @package vendor\models
 * @property String $pname_first
 * @property String $pname_middle
 * @property String $pname_last
 * @property String $private_phone
 * @property String $card_number
 * @property String $card_bank_id
 * @property String $login
 */
class Person
{
    public static $table = 'person';

    public static $fillable = [
        'pname_first', 'pname_middle', 'pname_last', 'private_phone', 'card_number', 'card_bank_id', 'login'
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