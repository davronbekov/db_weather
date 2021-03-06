<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 11:13 PM
 */


require '../../../autoload.php';

use vendor\models\Map;
use vendor\models\Provides;
use vendor\models\Service;

$errorClass = [
    'name' => '',
    'params_url' => '',
    'license' => '',
];
$service = Service::getItem(['name' => $_GET['name']]);
$maps = Map::getItems();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    Service::updateItem([
        'name' => $_POST['name'],
        'params_url' => $_POST['params_url'],
        'license' => $_POST['license'],
    ], [
        'name' => $_GET['name'],
    ]);

    Provides::deleteItem([
        'name' => $_POST['name'],
    ]);

    foreach ($_POST['provides'] as $map_id){
        Provides::insertItem([
            'map_id' => $map_id,
            'name' => $_POST['name'],
        ]);
    }

    header("Location: /db_weather/user/admin/service/list.php");
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
                <h3 class="title"> Edit service </h3>
            </div>

            <form method="post">
                <div class="card card-block">

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Name: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['name'] ?>" name="name" placeholder="Name" required value="<?= $service['name']?>">
                            <div class="invalid-feedback">Name already exists </div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Url: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['params_url'] ?>" name="params_url" placeholder="Url" required value="<?= $service['params_url']?>">
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> license: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['license'] ?>" name="license" placeholder="license" required value="<?= $service['license']?>">
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Maps: </label>
                        <select class="w-50" name="provides[]" multiple>
                            <?php
                            foreach ($maps as $map){ ?>
                                <option value="<?= $map['map_id']?>" <?= Provides::getItem(['map_id' => $map['map_id'], 'name' => $service['name']]) ? 'selected' : ''?>>
                                    <?= $map['description']?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
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
