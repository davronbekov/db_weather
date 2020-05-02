<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 10:53 AM
 */

require '../../../autoload.php';

use vendor\models\User;
use vendor\models\Admin;
use vendor\models\Person;
use vendor\models\Company;

$login = $_GET['login'];

$user = User::getItem(['login' => $_GET['login']]);
$user_type = User::getUserType($_GET['login']);

switch ($user_type){
    case 'admin':
        Admin::deleteItem(['login' => $_GET['login']]);
        break;
    case 'person':
        Person::deleteItem(['login' => $_GET['login']]);
        break;
    case 'company':
        Company::deleteItem(['login' => $_GET['login']]);
        break;
}

User::deleteItem(['login' => $_GET['login']]);

header("Location: /db_weather/user/admin/users/list.php");
exit();