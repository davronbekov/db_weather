<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 9:48 PM
 */

namespace vendor\models;

use vendor\libs\DB;

/**
 * Class User
 * @package vendor\libs\models
 * @property String $login
 * @property String $password
 * @property String $email
 * @property String $session_id
 * @property String $session_expire_time
 * @property int $balance
 */
class User
{
    public static $table = 'user';

    public static $fillable = [
        'login', 'password', 'email', 'session_id', 'session_expire_time', 'balance'
    ];

    public static function checkPassword($input_password, $user_password){
        $input_password = md5($input_password);
        return (Boolean) ($input_password == $user_password);
    }

    public static function getUserType($login){
        $admin = Admin::getItem(['login' => $login]);
        if($admin)
            return 'admin';

        $person = Person::getItem(['login' => $login]);
        if($person)
            return 'person';

        $company = Company::getItem(['login' => $login]);
        if($company)
            return 'company';
    }

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