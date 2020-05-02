<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 10:53 AM
 */

require '../../../autoload.php';

use vendor\models\Service;

Service::deleteItem(['name' => $_GET['name']]);

header("Location: /db_weather/user/admin/service/list.php");
exit();