<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 01-May-20
 * Time: 11:13 PM
 */


require '../../../autoload.php';

use vendor\models\Color_mapping;

$colors = Color_mapping::getItems();

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


        <article class="content items-list-page">

            <div class="title-search-block">
                <div class="title-block">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="title">
                                <a href="/db_weather/user/admin/colors/create.php" class="btn btn-primary btn-sm rounded-s"> Add New </a>
                            </h3>
                            <p class="title-description"> List of Colors</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card items">
                <ul class="item-list striped">
                    <li class="item item-list-header">
                        <div class="item-row">
                            <div class="item-col item-col-header item-col-author">
                                <div class="no-overflow">
                                    <span>Color</span>
                                </div>
                            </div>

                            <div class="item-col item-col-header item-col-author">
                                <div class="no-overflow">
                                    <span>Value</span>
                                </div>
                            </div>

                            <div class="item-col item-col-header item-col-author">
                                <div class="no-overflow">
                                    <span>Map id</span>
                                </div>
                            </div>
                            <div class="item-col item-col-header fixed item-col-actions-dropdown">
                            </div>
                        </div>
                    </li>
                    <?php
                        foreach ($colors as $color)
                        {
                            ?>
                            <li class="item">
                                <div class="item-row">
                                    <div class="item-col item-col-author">
                                        <div class="item-heading">Color</div>
                                        <div class="no-overflow">
                                            <h6><?= $color['color'];?></h6>
                                        </div>
                                    </div>

                                    <div class="item-col item-col-author">
                                        <div class="item-heading">Value</div>
                                        <div class="no-overflow">
                                            <h6><?= $color['value'];?></h6>
                                        </div>
                                    </div>

                                    <div class="item-col item-col-author">
                                        <div class="item-heading">Map id</div>
                                        <div class="no-overflow">
                                            <h6><?= $color['map_id'];?></h6>
                                        </div>
                                    </div>
                                    <div class="item-col fixed item-col-actions-dropdown">
                                        <div class="item-actions-dropdown">
                                            <a class="item-actions-toggle-btn">
                                                    <span class="inactive">
                                                        <i class="fa fa-cog"></i>
                                                    </span>
                                                    <span class="active">
                                                        <i class="fa fa-chevron-circle-right"></i>
                                                    </span>
                                            </a>
                                            <div class="item-actions-block">
                                                <ul class="item-actions-list">
                                                    <li>
                                                        <a class="remove" href="/db_weather/user/admin/colors/delete.php?map_id=<?= $color['map_id'] ?>&value=<?= $color['value'] ?>" onclick="return confirm('Do you really want to delete this user?')">
                                                            <i class="fa fa-trash-o "></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="edit" href="/db_weather/user/admin/colors/edit.php?map_id=<?= $color['map_id'] ?>&value=<?= $color['value'] ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php
                        }
                        ?>
                </ul>
            </div>

        </article>

    </div>
</div>

<script src="/db_weather/js/vendor.js"></script>
<script src="/db_weather/js/app.js"></script>
</body>
</html>
