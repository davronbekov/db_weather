<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 11:13 PM
 */


require '../../../autoload.php';

use vendor\models\User;
use vendor\models\Admin;
use vendor\models\Person;
use vendor\models\Company;

$errorClass = [
    'login' => '',
    'password' => '',
    'email' => '',
    'aname_first' => '',
    'aname_last' => '',
    'pname_first' => '',
    'pname_middle' => '',
    'pname_last' => '',
    'private_phone' => '',
    'card_number' => '',
    'cname' => '',
    'inn' => '',
    'address' => '',
    'zipcode' => '',
    'bank_account' => '',
    'bank_mfo' => '',
];

$user = User::getItem(['login' => $_GET['login']]);
$user_type = User::getUserType($_GET['login']);
$user_data = [];

switch ($user_type){
    case 'admin':
        $user_data = Admin::getItem(['login' => $_GET['login']]);
        break;
    case 'person':
        $user_data = Person::getItem(['login' => $_GET['login']]);
        break;
    case 'company':
        $user_data = Company::getItem(['login' => $_GET['login']]);
        break;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = User::updateItem([
        'password' => md5($_POST['password']),
        'email' => $_POST['email'],
    ], [
        'login' => $_POST['login'],
    ]);

    switch ($user_type){
        case 'admin':
            Admin::updateItem([
                'aname_first' => $_POST['aname_first'],
                'aname_last' => $_POST['aname_last'],
            ], [
                'login' => $_POST['login'],
            ]);
            break;
        case 'person':
            Person::updateItem([
                'pname_first' => $_POST['pname_first'],
                'pname_middle' => $_POST['pname_middle'],
                'pname_last' => $_POST['pname_last'],
                'private_phone' => $_POST['private_phone'],
                'card_number' => $_POST['card_number'],
            ], [
                'login' => $_POST['login'],
            ]);
            break;
        case 'company':
            Company::updateItem([
                'cname' => $_POST['cname'],
                'inn' => $_POST['inn'],
                'address' => $_POST['address'],
                'zipcode' => $_POST['zipcode'],
                'bank_account' => $_POST['bank_account'],
                'bank_mfo' => $_POST['bank_mfo'],
            ], [
                'login' => $_POST['login'],
            ]);
            break;
    }

    header("Location: /db_weather/user/admin/users/list.php");
    exit();
}

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> DB Weather </title>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="/db_weather/css/vendor.css">
    <link rel="stylesheet" href="/db_weather/css/app.css">
</head>
<body>
<div class="main-wrapper">
    <div class="app" id="app">

        <?php
            require '../pieces/header.php';
        ?>

        <?php
            require '../pieces/menu.php';
        ?>

        <article class="content item-editor-page">
            <div class="title-block">
                <h3 class="title"> Edit user </h3>
            </div>

            <form method="post">
                <div class="card card-block">

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Login: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['login'] ?>" name="login" placeholder="Login" required value="<?= $user['login'] ?>">
                            <div class="invalid-feedback">Login already exists </div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Email: </label>
                        <div class="col-sm-5">
                            <input type="email" class="form-control boxed <?= $errorClass['email'] ?>" name="email" placeholder="example@gmail.com" required value="<?= $user['email'] ?>">
                            <div class="invalid-feedback">Email already exists </div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Password: </label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control boxed <?= $errorClass['password'] ?>" name="password" placeholder="password" required>
                        </div>
                    </div>


                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> User type: </label>
                        <div class="col-sm-5">

                            <select class="w-100" name="user_type" id="user_type">
                                <option value="">-- not selected --</option>
                                <option value="admin" <?= $user_type == 'admin' ? 'selected' : ''?>>Admin</option>
                                <option value="person" <?= $user_type == 'person' ? 'selected' : ''?>>Person</option>
                                <option value="company" <?= $user_type == 'company' ? 'selected' : ''?>>Company</option>
                            </select>

                        </div>
                    </div>

                    <span id="admin_data" style="<?= $user_type == 'admin' ? '' : 'display:none;'?>">
                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> First name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['aname_first'] ?>" name="aname_first" placeholder="First name" value="<?= $user_data['aname_first'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> Last name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['aname_last'] ?>" name="aname_last" placeholder="Last name" value="<?= $user_data['aname_last'] ?>">
                            </div>
                        </div>
                    </span>

                    <span id="person_data" style="<?= $user_type == 'person' ? '' : 'display:none;'?>">
                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> First name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['pname_first'] ?>" name="pname_first" placeholder="First name" value="<?= $user_data['pname_first'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> Middle name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['pname_middle'] ?>" name="pname_middle" placeholder="Middle name" value="<?= $user_data['pname_middle'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> Last name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['pname_last'] ?>" name="pname_last" placeholder="Last name" value="<?= $user_data['pname_last'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> Phone number: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['private_phone'] ?>" name="private_phone" placeholder="+998971234567" value="<?= $user_data['private_phone'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> Card number: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['card_number'] ?>" name="card_number" placeholder="8600**********" value="<?= $user_data['card_number'] ?>">
                            </div>
                        </div>
                    </span>

                    <span id="company_data" style="<?= $user_type == 'company' ? '' : 'display:none;'?>">
                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> Company name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['cname'] ?>" name="cname" placeholder="Company name" value="<?= $user_data['cname'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> inn: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['inn'] ?>" name="inn" placeholder="inn" value="<?= $user_data['inn'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> address: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['address'] ?>" name="address" placeholder="address" value="<?= $user_data['address'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> zipcode: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['zipcode'] ?>" name="zipcode" placeholder="zipcode" value="<?= $user_data['zipcode'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> bank account: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['bank_account'] ?>" name="bank_account" placeholder="bank_account" value="<?= $user_data['bank_account'] ?>">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label class="col-sm-2 form-control-label text-xs-right"> bank mfo: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control boxed <?= $errorClass['bank_mfo'] ?>" name="bank_mfo" placeholder="bank_mfo" value="<?= $user_data['bank_mfo'] ?>">
                            </div>
                        </div>
                    </span>

                    <div class="form-group row justify-content-center">
                        <div class="col-sm-5 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary"> Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </article>

    </div>
</div>

<script src="/db_weather/js/vendor.js"></script>
<script src="/db_weather/js/app.js"></script>
</body>
</html>
