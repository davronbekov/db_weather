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
    'map_id' => '',
    'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $map_id = uniqid();

    Map::insertItem([
        'map_id' => $map_id,
        'description' => $_POST['description'],
    ]);

    foreach ($_POST['provides'] as $service){
        Provides::insertItem([
            'map_id' => $map_id,
            'name' => $service,
        ]);
    }

}

$services = Service::getItems();

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
                <h3 class="title"> Add new map </h3>
            </div>

            <form method="post">
                <div class="card card-block">

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Description: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['description'] ?>" name="description" placeholder="description" required>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Services: </label>
                        <select class="w-50" name="provides[]" multiple>
                            <?php
                            foreach ($services as $service){ ?>
                                <option value="<?= $service['name']?>"><?= $service['name']?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group row justify-content-center">
                        <div class="col-sm-5 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary"> Create</button>
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
