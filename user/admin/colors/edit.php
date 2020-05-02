<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 11:13 PM
 */


require '../../../autoload.php';

use vendor\models\Color_mapping;
use vendor\models\Map;

$errorClass = [
    'color' => '',
    'value' => '',
    'map_id' => '',
];

$color = Color_mapping::getItem(['map_id' => $_GET['map_id'], 'value' => $_GET['value']]);
$maps = Map::getItems();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    Color_mapping::updateItem([
        'color' => $_POST['color'],
        'value' => $_POST['value'],
        'map_id' => $_POST['map_id'],
    ], ['map_id' => $_GET['map_id'], 'value' => $_GET['value']]);

    header("Location: /db_weather/user/admin/colors/list.php");
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
                <h3 class="title"> Edit Color </h3>
            </div>

            <form method="post">
                <div class="card card-block">

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Color: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['color'] ?>" name="color" placeholder="#FFFFFF" required value="<?= $color['color'] ?>">
                            <div class="invalid-feedback">Color already exists </div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Value: </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control boxed <?= $errorClass['value'] ?>" name="value" placeholder="Value" required value="<?= $color['value'] ?>">
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="col-sm-2 form-control-label text-xs-right"> Map: </label>
                        <select class="w-25" name="map_id">
                            <?php
                            foreach ($maps as $map) {
                                ?>
                                <option value="<?= $map['map_id']?>" <?= $color['map_id'] == $map['map_id'] ? 'selected' : ''?>><?= $map['description'] ?></option>
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
