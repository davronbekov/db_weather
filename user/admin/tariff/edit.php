<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 11:13 PM
 */


require '../../../autoload.php';

use vendor\models\Tariff;

$errorClass = [
    'id' => '',
    'cost' => '',
    'duration' => '',
    'description' => '',
];

$tariff = Tariff::getItem(['id' => $_GET['id']]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = Tariff::updateItem([
        'cost' => $_POST['cost'],
        'duration' => $_POST['duration'],
        'description' => $_POST['description'],
    ], [
        'id' => $_GET['id']
    ]);

    header("Location: /db_weather/user/admin/tariff/list.php");
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
                <h3 class="title"> Edit tariff </h3>
            </div>

            <form method="post">
                <div class="card card-block">

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Cost: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['cost'] ?>" name="cost" placeholder="cost" required value="<?= $tariff['cost']; ?>">
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Duration: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['duration'] ?>" name="duration" placeholder="3" required value="<?= $tariff['duration']; ?>">
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Description: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['description'] ?>" name="description" placeholder="description" required value="<?= $tariff['description']; ?>">
                        </div>
                    </div>


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
