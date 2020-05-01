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
 * Class Company
 * @package vendor\models
 * @property String $cname
 * @property String $inn
 * @property String $address
 * @property String $zipcode
 * @property String $bank_account
 * @property String $bank_mfo
 * @property String $login
 */
class Company
{
    public static $table = 'company';

    public static $fillable = [
        'cname', 'inn', 'address', 'zipcode', 'bank_account', 'bank_mfo', 'login'
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