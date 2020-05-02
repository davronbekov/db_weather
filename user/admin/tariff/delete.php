<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 10:53 AM
 */

require '../../../autoload.php';

use vendor\models\Tariff;

Tariff::deleteItem(['id' => $_GET['id']]);

header("Location: /db_weather/user/admin/tariff/list.php");
exit();